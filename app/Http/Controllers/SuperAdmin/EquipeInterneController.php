<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Agency;
use App\Models\User;
use App\Services\CommissionService;
use App\Support\PasswordPolicy;
use App\Support\SuperAdminContext;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Module Super Admin « Équipe interne ».
 *
 * Gère les comptes ayant accès au Super Admin : l'admin PRINCIPAL (accès total)
 * et les COLLABORATEURS à accès restreint (périmètre = leurs agences apportées).
 *
 * Toutes ces routes sont réservées à l'admin principal (middleware sa.section:equipe) :
 * un collaborateur ne gère jamais l'équipe, ne se réattribue pas d'agences et ne voit
 * pas les autres comptes.
 */
class EquipeInterneController extends Controller
{
    public function __construct(
        private CommissionService $commissions,
        private SuperAdminContext $context,
    ) {}

    /** Écran principal : une carte par compte ayant accès au Super Admin. */
    public function index(): View
    {
        $principal = Auth::user();

        $collaborateurs = User::collaborateursSa()
            ->orderBy('name')
            ->get()
            ->map(function (User $u) {
                $ligne = $this->commissions->actuelle($u);
                // Faute de colonne « dernière connexion », on dérive la dernière action
                // tracée dans le journal (source durable, cohérente avec le dashboard).
                $derniere = ActivityLog::where('user_id', $u->id)->max('created_at');

                return [
                    'user'       => $u,
                    'nb_agences' => $ligne['nb_agences'],
                    'mrr'        => $ligne['mrr_total'],
                    'taux'       => $ligne['taux'],
                    'commission' => $ligne['commission'],
                    'revoque'    => $u->saAccesRevoque(),
                    'derniere'   => $derniere ? \Carbon\Carbon::parse($derniere) : null,
                ];
            });

        return view('superadmin.equipe.index', [
            'principal'      => $principal,
            'collaborateurs' => $collaborateurs,
            'permissionsMeta' => $this->permissionsMeta(),
        ]);
    }

    /** Libellés + défauts des 4 toggles de permission (source unique). */
    private function permissionsMeta(): array
    {
        return [
            'voir_agences'    => 'Voir ses agences attribuées',
            'impersonation'   => 'Impersonation (limitée à ses agences)',
            'facturation'     => 'Facturation globale plateforme',
            'regles_fiscales' => 'Règles fiscales',
        ];
    }

    // ── Invitation d'un collaborateur ───────────────────────────────────────

    public function create(): View
    {
        return view('superadmin.equipe.invite');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'password'        => ['required', 'confirmed', PasswordPolicy::rules()],
            'taux_commission' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'email.unique'            => 'Cet email est déjà utilisé par un compte actif.',
            'taux_commission.required' => 'Indiquez le taux de commission convenu avec ce collaborateur.',
        ]);

        // role et sa_* sont hors $fillable → assignation directe contrôlée.
        $user = new User;
        $user->name              = $validated['name'];
        $user->email             = $validated['email'];
        $user->password          = Hash::make($validated['password']);
        $user->role              = 'superadmin';
        $user->email_verified_at = now();
        $user->save();

        // Privilèges plateforme (assignés après save pour rester hors mass-assignment).
        $user->forceFill([
            'sa_est_principal'   => false,
            'sa_taux_commission' => $validated['taux_commission'],
            // Défauts du brief : voir agences + impersonation ON, facturation + règles OFF.
            'sa_permissions'     => User::SA_PERMISSIONS,
            'must_change_password' => true, // le collaborateur définit son mot de passe à la 1ʳᵉ connexion
        ])->save();

        $this->journaliser('equipe_collaborateur_invite', $user,
            "Collaborateur ajouté au Super Admin : {$user->name} (commission {$validated['taux_commission']} %)");

        return redirect()
            ->route('superadmin.equipe.index')
            ->with('success', "{$user->name} a été ajouté à l'équipe. Communiquez-lui ses identifiants de connexion en sécurité.");
    }

    // ── Toggles de permission ───────────────────────────────────────────────

    public function togglePermission(Request $request, User $collaborateur): RedirectResponse
    {
        $this->assertCollaborateur($collaborateur);

        $cle = $request->input('permission');
        abort_unless(array_key_exists($cle, User::SA_PERMISSIONS), 422);

        $perms = $collaborateur->sa_permissions ?? User::SA_PERMISSIONS;
        $perms[$cle] = ! ($perms[$cle] ?? User::SA_PERMISSIONS[$cle]);
        $collaborateur->forceFill(['sa_permissions' => $perms])->save();

        $etat = $perms[$cle] ? 'activée' : 'désactivée';

        return back()->with('success', "Permission « {$this->permissionsMeta()[$cle]} » {$etat} pour {$collaborateur->name}.");
    }

    /** Met à jour le taux de commission d'un collaborateur. */
    public function updateTaux(Request $request, User $collaborateur): RedirectResponse
    {
        $this->assertCollaborateur($collaborateur);

        $valide = $request->validate([
            'taux_commission' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $collaborateur->forceFill(['sa_taux_commission' => $valide['taux_commission']])->save();

        return back()->with('success', "Taux de commission de {$collaborateur->name} fixé à {$valide['taux_commission']} %.");
    }

    // ── Révocation / restauration d'accès ───────────────────────────────────

    /**
     * Coupe l'accès Super Admin du collaborateur.
     * Ne détache PAS ses agences (amenee_par inchangé) : l'historique de commission
     * est préservé. La réattribution est une action séparée et volontaire.
     */
    public function revoquer(User $collaborateur): RedirectResponse
    {
        $this->assertCollaborateur($collaborateur);

        if ($collaborateur->saAccesRevoque()) {
            return back()->with('error', "L'accès de {$collaborateur->name} est déjà révoqué.");
        }

        $collaborateur->forceFill(['sa_acces_revoque_at' => now()])->save();

        // Si l'admin principal observait ce collaborateur, on sort du mode « Voir comme ».
        if ($this->context->collaborateurObserve()?->id === $collaborateur->id) {
            $this->context->arreterVoirComme();
        }

        $this->journaliser('equipe_acces_revoque', $collaborateur,
            "Accès Super Admin révoqué : {$collaborateur->name} (agences conservées)");

        return back()->with('success', "Accès de {$collaborateur->name} révoqué. Ses agences restent attribuées pour l'historique de commission.");
    }

    /** Rétablit l'accès d'un collaborateur précédemment révoqué. */
    public function restaurer(User $collaborateur): RedirectResponse
    {
        $this->assertCollaborateur($collaborateur);

        $collaborateur->forceFill(['sa_acces_revoque_at' => null])->save();

        $this->journaliser('equipe_acces_restaure', $collaborateur,
            "Accès Super Admin rétabli : {$collaborateur->name}");

        return back()->with('success', "Accès de {$collaborateur->name} rétabli.");
    }

    // ── Réattribution des agences ───────────────────────────────────────────

    public function editReattribution(User $collaborateur): View
    {
        $this->assertCollaborateur($collaborateur);

        // Agences actuellement attribuées à ce collaborateur (hors scope de périmètre).
        $agences = Agency::sansPerimetre()
            ->where('amenee_par', $collaborateur->id)
            ->orderBy('name')
            ->get();

        // Cibles possibles : autres comptes super-admin (principal + collaborateurs).
        $cibles = User::where('role', 'superadmin')
            ->where('id', '!=', $collaborateur->id)
            ->orderByDesc('sa_est_principal')
            ->orderBy('name')
            ->get();

        return view('superadmin.equipe.reattribuer', compact('collaborateur', 'agences', 'cibles'));
    }

    public function reattribuer(Request $request, User $collaborateur): RedirectResponse
    {
        $this->assertCollaborateur($collaborateur);

        $mode = $request->input('mode', 'masse');

        // Cibles valides : « non attribué » (vide) ou un compte super-admin existant.
        $superAdminIds = User::where('role', 'superadmin')->pluck('id')->all();
        $estCible = fn ($v) => $v === null || $v === '' || in_array((int) $v, $superAdminIds, true);
        $normalise = fn ($v) => ($v === null || $v === '') ? null : (int) $v;

        if ($mode === 'masse') {
            $valide = $request->validate([
                'cible' => ['nullable', Rule::in(array_map('strval', $superAdminIds))],
            ]);
            $cible = $normalise($valide['cible'] ?? null);

            $n = Agency::sansPerimetre()
                ->where('amenee_par', $collaborateur->id)
                ->update(['amenee_par' => $cible]);

            $nom = $cible ? (User::find($cible)?->name ?? 'un autre compte') : 'Non attribué';
            $this->journaliser('equipe_reattribution', $collaborateur,
                "Réattribution en masse des {$n} agences de {$collaborateur->name} → {$nom}");

            return redirect()->route('superadmin.equipe.index')
                ->with('success', "{$n} agence(s) réattribuée(s) de {$collaborateur->name} vers {$nom}.");
        }

        // Mode individuel : map agency_id => nouvel amenee_par.
        $affectations = (array) $request->input('agences', []);
        $modifiees = 0;

        foreach ($affectations as $agencyId => $nouvelle) {
            if (! $estCible($nouvelle)) {
                continue;
            }
            $agence = Agency::sansPerimetre()
                ->where('id', (int) $agencyId)
                ->where('amenee_par', $collaborateur->id) // on ne touche que SES agences
                ->first();
            if (! $agence) {
                continue;
            }
            $cible = $normalise($nouvelle);
            if ($agence->amenee_par !== $cible) {
                $agence->amenee_par = $cible;
                $agence->save();
                $modifiees++;
            }
        }

        $this->journaliser('equipe_reattribution', $collaborateur,
            "Réattribution individuelle : {$modifiees} agence(s) de {$collaborateur->name}");

        return redirect()->route('superadmin.equipe.index')
            ->with('success', $modifiees > 0
                ? "{$modifiees} agence(s) réattribuée(s)."
                : 'Aucune modification.');
    }

    // ── « Voir comme [collaborateur] » ──────────────────────────────────────

    public function voirComme(User $collaborateur): RedirectResponse
    {
        $this->assertCollaborateur($collaborateur);

        if ($collaborateur->saAccesRevoque()) {
            return back()->with('error', "Impossible d'observer un collaborateur dont l'accès est révoqué.");
        }

        $this->context->demarrerVoirComme($collaborateur);

        return redirect()->route('superadmin.dashboard')
            ->with('success', "Vous voyez maintenant le Super Admin comme {$collaborateur->name}. Cliquez sur « Revenir à ma vue » pour sortir.");
    }

    public function arreterVoirComme(): RedirectResponse
    {
        $this->context->arreterVoirComme();

        return redirect()->route('superadmin.equipe.index')
            ->with('success', 'Vous êtes de retour sur votre vue complète.');
    }

    // ── Historique des commissions ──────────────────────────────────────────

    public function historique(User $collaborateur): View
    {
        $this->assertCollaborateur($collaborateur);

        return view('superadmin.equipe.commissions', [
            'collaborateur' => $collaborateur,
            'lignes'        => $this->commissions->historique($collaborateur),
        ]);
    }

    public function historiquePdf(User $collaborateur)
    {
        $this->assertCollaborateur($collaborateur);

        $pdf = Pdf::loadView('superadmin.pdf.commissions', [
            'collaborateur' => $collaborateur,
            'lignes'        => $this->commissions->historique($collaborateur),
            'genereLe'      => now(),
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isRemoteEnabled', false);

        return $pdf->download('commissions-'.\Illuminate\Support\Str::slug($collaborateur->name).'-'.now()->format('Y-m-d').'.pdf');
    }

    public function historiqueCsv(User $collaborateur): StreamedResponse
    {
        $this->assertCollaborateur($collaborateur);

        $lignes = $this->commissions->historique($collaborateur);
        $colonnes = ['Mois', 'Nb agences', 'MRR total (F)', 'Taux (%)', 'Commission (F)', 'Statut'];

        return response()->streamDownload(function () use ($lignes, $colonnes) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel
            fputcsv($handle, $colonnes, ';');

            foreach ($lignes as $l) {
                fputcsv($handle, [
                    $l['mois']->locale('fr')->isoFormat('MMMM Y'),
                    $l['nb_agences'],
                    $l['mrr_total'],
                    number_format($l['taux'], 2, ',', ''),
                    $l['commission'],
                    $l['fige'] ? 'Figé' : 'En cours',
                ], ';');
            }

            fclose($handle);
        }, 'commissions-'.\Illuminate\Support\Str::slug($collaborateur->name).'-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ── Gardes & utilitaires ────────────────────────────────────────────────

    /** La cible doit être un collaborateur super-admin restreint (jamais le principal). */
    private function assertCollaborateur(User $user): void
    {
        abort_unless($user->estCollaborateurSa(), 404, 'Ce compte n\'est pas un collaborateur.');
    }

    private function journaliser(string $action, User $collaborateur, string $description): void
    {
        ActivityLog::create([
            'user_id'      => Auth::id(),
            'agency_id'    => null,
            'action'       => $action,
            'model_type'   => User::class,
            'model_id'     => $collaborateur->id,
            'description'  => $description,
            'is_sensitive' => true,
            'ip_address'   => request()?->ip(),
        ]);
    }
}

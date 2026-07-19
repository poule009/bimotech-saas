<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Support\JournalCritique;
use App\Support\PlatformSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Module Super Admin « Paramètres système » (7ᵉ et dernière section).
 *
 * Réservé à l'admin principal (middleware sa.section:parametres). Trois volets :
 *   1. Général  — infos support + statut des intégrations + mode maintenance ;
 *   2. Sécurité — options de sécurité des comptes admin (persistées ici,
 *                 enforcement runtime câblé dans une itération ultérieure) ;
 *   3. Journal d'activité critique — VUE en lecture seule de activity_logs,
 *                 filtrée sur les codes d'action critiques (voir JournalCritique).
 *
 * Les clés API des paiements ne sont volontairement PAS gérées ici (secrets en
 * variables d'environnement / Forge) : on n'affiche qu'un statut de connexion.
 */
class ParametresController extends Controller
{
    public function __construct(private PlatformSettings $settings) {}

    public function index(Request $request): View
    {
        // ── Journal critique (lecture seule, filtres serveur) ──────────────
        $severite = (string) $request->get('severite', 'toutes');
        $adminId  = (string) $request->get('admin', 'tous');

        $journalQuery = JournalCritique::query()->with(['user:id,name', 'agency:id,name']);

        if (array_key_exists($severite, JournalCritique::SEVERITES)) {
            $journalQuery->whereIn('action', JournalCritique::actionsPourSeverite($severite));
        }
        if (ctype_digit($adminId)) {
            $journalQuery->where('user_id', (int) $adminId);
        }

        $journal = $journalQuery->paginate(20)->withQueryString();

        // Administrateurs ayant réellement produit une action critique (options du filtre).
        $adminIds = ActivityLog::whereIn('action', array_keys(JournalCritique::ACTIONS))
            ->whereNotNull('user_id')->distinct()->pluck('user_id')->all();
        $admins = User::whereIn('id', $adminIds)->orderBy('name')->get(['id', 'name']);

        // Onglet actif au chargement : « Journal » si l'on filtre, sinon le paramètre tab.
        $filtreJournal = $request->hasAny(['severite', 'admin']);
        $tab = $filtreJournal ? 'journal' : (string) $request->get('tab', 'general');

        return view('superadmin.parametres.index', [
            'settings'      => $this->settings,
            'integrations'  => $this->integrations(),
            'journal'       => $journal,
            'admins'        => $admins,
            'severites'     => JournalCritique::SEVERITES,
            'severiteFiltre'=> $severite,
            'adminFiltre'   => $adminId,
            'tab'           => in_array($tab, ['general', 'securite', 'journal'], true) ? $tab : 'general',
        ]);
    }

    /** Volet Général — infos support. */
    public function updateGeneral(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'plateforme_nom'    => ['nullable', 'string', 'max:100'],
            'support_email'     => ['nullable', 'email', 'max:150'],
            'support_telephone' => ['nullable', 'string', 'max:40'],
        ]);

        $this->settings->setMany([
            'plateforme_nom'    => trim((string) ($data['plateforme_nom'] ?? '')),
            'support_email'     => trim((string) ($data['support_email'] ?? '')),
            'support_telephone' => trim((string) ($data['support_telephone'] ?? '')),
        ]);

        $this->journaliser('Informations générales de la plateforme mises à jour');

        return redirect()->route('superadmin.parametres.index', ['tab' => 'general'])
            ->with('success', 'Informations générales enregistrées.');
    }

    /** Volet Général — mode maintenance (blocage total côté agences). */
    public function updateMaintenance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'maintenance_active'  => ['nullable', 'boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:500'],
        ]);

        $actifAvant = $this->settings->maintenanceActive();
        $actif      = $request->boolean('maintenance_active');

        $this->settings->setMany([
            'maintenance_active'  => $actif,
            'maintenance_message' => trim((string) ($data['maintenance_message'] ?? '')),
        ]);

        if ($actif !== $actifAvant) {
            $this->journaliser($actif
                ? 'Mode maintenance ACTIVÉ — accès agences bloqué'
                : 'Mode maintenance désactivé — accès agences rétabli');
        }

        return redirect()->route('superadmin.parametres.index', ['tab' => 'general'])
            ->with('success', $actif
                ? 'Mode maintenance activé : les agences voient une page d\'attente.'
                : 'Mode maintenance désactivé : la plateforme est de nouveau accessible.');
    }

    /** Volet Sécurité — options de sécurité des comptes admin (persistées). */
    public function updateSecurite(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'securite_2fa_obligatoire'    => ['nullable', 'boolean'],
            'securite_session_expiration' => ['nullable', 'boolean'],
            'securite_session_minutes'    => ['nullable', 'integer', 'min:5', 'max:240'],
            'securite_mdp_renforce'       => ['nullable', 'boolean'],
        ]);

        $this->settings->setMany([
            'securite_2fa_obligatoire'    => $request->boolean('securite_2fa_obligatoire'),
            'securite_session_expiration' => $request->boolean('securite_session_expiration'),
            'securite_session_minutes'    => (int) ($data['securite_session_minutes'] ?? 30),
            'securite_mdp_renforce'       => $request->boolean('securite_mdp_renforce'),
        ]);

        $this->journaliser('Réglages de sécurité des comptes admin mis à jour');

        return redirect()->route('superadmin.parametres.index', ['tab' => 'securite'])
            ->with('success', 'Réglages de sécurité enregistrés.');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Statut des intégrations — dérivé de la PRÉSENCE des clés en configuration
     * serveur (jamais les clés elles-mêmes). Honnête : ce n'est pas un ping live,
     * mais l'état de configuration. Wave & Orange Money transitent par PayTech.
     *
     * @return array<int,array<string,string>>
     */
    private function integrations(): array
    {
        $paytech = config('services.paytech');
        $mode    = $paytech['mode'] ?? 'simulation';
        $clesOk  = ! empty($paytech['api_key']) && ! empty($paytech['api_secret']);

        [$payLabel, $payVariant] = match (true) {
            $mode === 'prod' && $clesOk => ['Actif (production)', 'green'],
            $mode === 'prod'            => ['Production — clés manquantes', 'gold'],
            $mode === 'test'            => ['Bac à sable (test)', 'gold'],
            default                     => ['Simulation (développement)', 'gray'],
        };

        $mailer   = (string) config('mail.default', 'log');
        $mailReel = ! in_array($mailer, ['log', 'array', ''], true);

        return [
            [
                'nom'     => 'PayTech',
                'desc'    => 'Agrégateur mobile — Wave, Orange Money, cartes',
                'label'   => $payLabel,
                'variant' => $payVariant,
            ],
            [
                'nom'     => 'Service d\'email',
                'desc'    => 'Transporteur : '.$mailer,
                'label'   => $mailReel ? 'Configuré' : 'Non configuré (dev)',
                'variant' => $mailReel ? 'green' : 'gray',
            ],
        ];
    }

    /** Trace une modification des paramètres système dans le journal critique. */
    private function journaliser(string $description): void
    {
        ActivityLog::create([
            'user_id'      => Auth::id(),
            'agency_id'    => null,
            'action'       => 'params_modifies',
            'is_sensitive' => true,
            'description'  => $description,
            'model_type'   => PlatformSetting::class,
            'model_id'     => 0,
            'ip_address'   => request()?->ip(),
        ]);
    }
}

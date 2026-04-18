#!/usr/bin/env bash
# ============================================================
# BIMO-Tech — Suite de tests fonctionnels (curl)
# ============================================================
BASE="http://localhost:8000"
COOKIEFILE_ADMIN="bimo_admin_cookies.txt"
COOKIEFILE_SA="bimo_sa_cookies.txt"
COOKIEFILE_LOC="bimo_loc_cookies.txt"

PASS=0; FAIL=0; WARN=0
declare -a RESULTS

assert() {
  local label="$1" actual="$2" expected="$3" detail="$4"
  if echo "$actual" | grep -qE "$expected"; then
    PASS=$((PASS+1))
    RESULTS+=("  OK $label")
  else
    FAIL=$((FAIL+1))
    RESULTS+=("  KO $label -- attendu: '$expected' | obtenu: '$detail'")
  fi
}

warn() {
  WARN=$((WARN+1))
  RESULTS+=("  WW $1")
}

section() { RESULTS+=(""); RESULTS+=("### $1"); }

get_csrf() {
  local cookiefile="$1" url="${2:-$BASE/login}"
  curl -sb "$cookiefile" -c "$cookiefile" "$url" 2>/dev/null | grep -oP '(?<=name="_token" value=")[^"]+' | head -1
}

http_code_of() {
  echo "$1" | awk -F'[|][|][|]' '{print $NF}'
}

login_user() {
  local email="$1" pass="$2" cookiefile="$3"
  local csrf
  csrf=$(curl -sc "$cookiefile" "$BASE/login" 2>/dev/null | grep -oP '(?<=name="_token" value=")[^"]+' | head -1)
  curl -sb "$cookiefile" -c "$cookiefile" -s -o /dev/null -w "%{http_code}|%{redirect_url}" \
    -X POST "$BASE/login" \
    --data-urlencode "email=$email" \
    --data-urlencode "password=$pass" \
    --data-urlencode "_token=$csrf" \
    -H "Content-Type: application/x-www-form-urlencoded"
}

get_page() {
  local url="$1" cookiefile="$2"
  curl -sb "$cookiefile" -s -w "|||%{http_code}" "$url" 2>/dev/null
}

tinker_get() {
  php artisan tinker --execute="$1" 2>/dev/null | tr -d '\n'
}

# ============================================================
# MODULE 1 — AUTHENTIFICATION
# ============================================================
section "MODULE 1 -- Authentification"

resp=$(curl -s -o /dev/null -w "%{http_code}" "$BASE/login")
assert "Page /login accessible (200)" "$resp" "^200$" "$resp"

rm -f bimo_bad_cookies.txt
resp=$(login_user "mauvais@email.com" "mauvais_pass" "bimo_bad_cookies.txt")
code=$(echo "$resp" | cut -d'|' -f1)
assert "Login mauvais identifiants -- rejet (302/422)" "$code" "^(302|422)$" "$code"

resp_after=$(curl -sb "bimo_bad_cookies.txt" -s -o /dev/null -w "%{http_code}" "$BASE/admin/dashboard")
assert "Mauvais login -- pas acces dashboard (302)" "$resp_after" "^302$" "$resp_after"

rm -f "$COOKIEFILE_ADMIN"
resp=$(login_user "gerant@test.sn" "password" "$COOKIEFILE_ADMIN")
code=$(echo "$resp" | cut -d'|' -f1)
assert "Login admin valide -- redirection (302)" "$code" "^302$" "$code"

resp=$(get_page "$BASE/admin/dashboard" "$COOKIEFILE_ADMIN")
http_code=$(http_code_of "$\1")
assert "Dashboard admin accessible apres login (200)" "$http_code" "^200$" "$http_code"

rm -f bimo_empty_cookies.txt
resp=$(login_user "" "" "bimo_empty_cookies.txt")
code=$(echo "$resp" | cut -d'|' -f1)
assert "Login champs vides -- rejet (302/422)" "$code" "^(302|422)$" "$code"

# ============================================================
# MODULE 2 — GESTION DES BIENS
# ============================================================
section "MODULE 2 -- Gestion des biens"

resp=$(get_page "$BASE/admin/biens" "$COOKIEFILE_ADMIN")
code=$(http_code_of "$\1")
assert "Liste des biens accessible (200)" "$code" "^200$" "$code"

resp=$(get_page "$BASE/admin/biens/create" "$COOKIEFILE_ADMIN")
code=$(http_code_of "$\1")
assert "Formulaire creation bien accessible (200)" "$code" "^200$" "$code"

CSRF_ADMIN=$(get_csrf "$COOKIEFILE_ADMIN" "$BASE/admin/biens/create")
PROP_ID=$(tinker_get "echo App\Models\User::where('role','proprietaire')->whereNotNull('email_verified_at')->first()->id;")

resp_create=$(curl -sb "$COOKIEFILE_ADMIN" -c "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" \
  -X POST "$BASE/admin/biens" \
  --data-urlencode "_token=$CSRF_ADMIN" \
  --data-urlencode "proprietaire_id=$PROP_ID" \
  --data-urlencode "type=appartement" \
  --data-urlencode "adresse=123 Rue de Test Curl" \
  --data-urlencode "ville=Dakar" \
  --data-urlencode "quartier=Test-Quartier" \
  --data-urlencode "loyer_mensuel=200000" \
  --data-urlencode "taux_commission=10" \
  --data-urlencode "surface_m2=75" \
  --data-urlencode "nombre_pieces=3" \
  -H "Content-Type: application/x-www-form-urlencoded")
assert "Creer un bien valide -- succes (302)" "$resp_create" "^302$" "$resp_create"

NEW_BIEN_ID=$(tinker_get "echo App\Models\Bien::where('adresse','123 Rue de Test Curl')->first()->id ?? 'not_found';")

CSRF_BAD=$(get_csrf "$COOKIEFILE_ADMIN" "$BASE/admin/biens/create")
resp_missing=$(curl -sb "$COOKIEFILE_ADMIN" -c "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" \
  -X POST "$BASE/admin/biens" \
  --data-urlencode "_token=$CSRF_BAD" \
  --data-urlencode "type=appartement" \
  -H "Content-Type: application/x-www-form-urlencoded")
assert "Creer bien champs manquants -- validation (302/422)" "$resp_missing" "^(302|422)$" "$resp_missing"

if [ "$NEW_BIEN_ID" != "not_found" ] && [ -n "$NEW_BIEN_ID" ]; then
  resp=$(get_page "$BASE/admin/biens/$NEW_BIEN_ID/edit" "$COOKIEFILE_ADMIN")
  code=$(http_code_of "$\1")
  assert "Formulaire edition bien accessible (200)" "$code" "^200$" "$code"

  CSRF_EDIT=$(get_csrf "$COOKIEFILE_ADMIN" "$BASE/admin/biens/$NEW_BIEN_ID/edit")
  resp_edit=$(curl -sb "$COOKIEFILE_ADMIN" -c "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" \
    -X POST "$BASE/admin/biens/$NEW_BIEN_ID" \
    --data-urlencode "_token=$CSRF_EDIT" \
    --data-urlencode "_method=PUT" \
    --data-urlencode "proprietaire_id=$PROP_ID" \
    --data-urlencode "type=appartement" \
    --data-urlencode "adresse=123 Rue de Test Modifie" \
    --data-urlencode "ville=Dakar" \
    --data-urlencode "quartier=Test-Quartier" \
    --data-urlencode "loyer_mensuel=250000" \
    --data-urlencode "taux_commission=10" \
    --data-urlencode "surface_m2=80" \
    --data-urlencode "nombre_pieces=3" \
    -H "Content-Type: application/x-www-form-urlencoded")
  assert "Modifier un bien -- succes (302)" "$resp_edit" "^302$" "$resp_edit"

  CSRF_DEL=$(get_csrf "$COOKIEFILE_ADMIN" "$BASE/admin/biens")
  resp_del=$(curl -sb "$COOKIEFILE_ADMIN" -c "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" \
    -X POST "$BASE/admin/biens/$NEW_BIEN_ID" \
    --data-urlencode "_token=$CSRF_DEL" \
    --data-urlencode "_method=DELETE" \
    -H "Content-Type: application/x-www-form-urlencoded")
  assert "Archiver un bien -- succes (302)" "$resp_del" "^302$" "$resp_del"
else
  warn "Bien de test non cree (props manquants?), edition/archivage non testes"
fi

# ============================================================
# MODULE 3 — GESTION DES LOCATAIRES
# ============================================================
section "MODULE 3 -- Gestion des locataires"

resp=$(get_page "$BASE/admin/users/locataires" "$COOKIEFILE_ADMIN")
code=$(http_code_of "$\1")
assert "Liste des locataires accessible (200)" "$code" "^200$" "$code"

resp=$(get_page "$BASE/admin/users/create/locataire" "$COOKIEFILE_ADMIN")
code=$(http_code_of "$\1")
assert "Formulaire creation locataire accessible (200)" "$code" "^200$" "$code"

CSRF_LOC=$(get_csrf "$COOKIEFILE_ADMIN" "$BASE/admin/users/create/locataire")
resp_create_loc=$(curl -sb "$COOKIEFILE_ADMIN" -c "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" \
  -X POST "$BASE/admin/users/store" \
  --data-urlencode "_token=$CSRF_LOC" \
  --data-urlencode "role=locataire" \
  --data-urlencode "name=Test Locataire Curl" \
  --data-urlencode "email=testlocataire.curl@test.sn" \
  --data-urlencode "password=password123" \
  --data-urlencode "telephone=+221700000001" \
  -H "Content-Type: application/x-www-form-urlencoded")
assert "Creer locataire valide -- succes (302)" "$resp_create_loc" "^(302|200)$" "$resp_create_loc"

NEW_LOC_ID=$(tinker_get "echo App\Models\User::where('email','testlocataire.curl@test.sn')->first()->id ?? 'not_found';")

CSRF_LOC2=$(get_csrf "$COOKIEFILE_ADMIN" "$BASE/admin/users/create/locataire")
resp_bad_loc=$(curl -sb "$COOKIEFILE_ADMIN" -c "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" \
  -X POST "$BASE/admin/users/store" \
  --data-urlencode "_token=$CSRF_LOC2" \
  --data-urlencode "role=locataire" \
  --data-urlencode "name=Bad" \
  --data-urlencode "email=not-an-email" \
  --data-urlencode "password=password123" \
  -H "Content-Type: application/x-www-form-urlencoded")
assert "Creer locataire email invalide -- validation (302/422)" "$resp_bad_loc" "^(302|422)$" "$resp_bad_loc"

if [ "$NEW_LOC_ID" != "not_found" ] && [ -n "$NEW_LOC_ID" ]; then
  resp=$(get_page "$BASE/admin/users/$NEW_LOC_ID/edit" "$COOKIEFILE_ADMIN")
  code=$(http_code_of "$\1")
  assert "Formulaire edition locataire (200)" "$code" "^200$" "$code"

  CSRF_EDIT_LOC=$(get_csrf "$COOKIEFILE_ADMIN" "$BASE/admin/users/$NEW_LOC_ID/edit")
  resp_edit_loc=$(curl -sb "$COOKIEFILE_ADMIN" -c "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" \
    -X POST "$BASE/admin/users/$NEW_LOC_ID" \
    --data-urlencode "_token=$CSRF_EDIT_LOC" \
    --data-urlencode "_method=PATCH" \
    --data-urlencode "name=Test Locataire Modifie" \
    --data-urlencode "email=testlocataire.curl@test.sn" \
    --data-urlencode "telephone=+221700000002" \
    -H "Content-Type: application/x-www-form-urlencoded")
  assert "Modifier locataire -- succes (200/302)" "$resp_edit_loc" "^(200|302)$" "$resp_edit_loc"

  CSRF_DEL_LOC=$(get_csrf "$COOKIEFILE_ADMIN" "$BASE/admin/users")
  resp_del_loc=$(curl -sb "$COOKIEFILE_ADMIN" -c "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" \
    -X POST "$BASE/admin/users/$NEW_LOC_ID" \
    --data-urlencode "_token=$CSRF_DEL_LOC" \
    --data-urlencode "_method=DELETE" \
    -H "Content-Type: application/x-www-form-urlencoded")
  assert "Supprimer locataire -- succes (302)" "$resp_del_loc" "^(200|302)$" "$resp_del_loc"
else
  warn "Locataire de test non cree, edition/suppression non testees"
fi

# ============================================================
# MODULE 4 — CONTRATS
# ============================================================
section "MODULE 4 -- Contrats"

resp=$(get_page "$BASE/admin/contrats" "$COOKIEFILE_ADMIN")
code=$(http_code_of "$\1")
assert "Liste contrats accessible (200)" "$code" "^200$" "$code"

resp=$(get_page "$BASE/admin/contrats/create" "$COOKIEFILE_ADMIN")
code=$(http_code_of "$\1")
assert "Formulaire creation contrat accessible (200)" "$code" "^200$" "$code"

CSRF_CONTRAT=$(get_csrf "$COOKIEFILE_ADMIN" "$BASE/admin/contrats/create")
resp_bad_contrat=$(curl -sb "$COOKIEFILE_ADMIN" -c "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" \
  -X POST "$BASE/admin/contrats" \
  --data-urlencode "_token=$CSRF_CONTRAT" \
  -H "Content-Type: application/x-www-form-urlencoded")
assert "Creer contrat vide -- validation (302/422)" "$resp_bad_contrat" "^(302|422)$" "$resp_bad_contrat"

BIEN_DISPO=$(tinker_get "echo App\Models\Bien::where('statut','disponible')->first()->id ?? '';")
LOC_LIBRE=$(tinker_get "
\$locs = App\Models\User::where('role','locataire')->whereNotNull('email_verified_at')->get();
foreach(\$locs as \$l) {
  if(!App\Models\Contrat::where('locataire_id',\$l->id)->where('statut','actif')->exists()) {
    echo \$l->id;
    break;
  }
}")

if [ -n "$BIEN_DISPO" ] && [ -n "$LOC_LIBRE" ]; then
  CSRF_CONTRAT2=$(get_csrf "$COOKIEFILE_ADMIN" "$BASE/admin/contrats/create")
  TODAY=$(date +%Y-%m-%d)
  resp_contrat=$(curl -sb "$COOKIEFILE_ADMIN" -c "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" \
    -X POST "$BASE/admin/contrats" \
    --data-urlencode "_token=$CSRF_CONTRAT2" \
    --data-urlencode "bien_id=$BIEN_DISPO" \
    --data-urlencode "locataire_id=$LOC_LIBRE" \
    --data-urlencode "date_debut=$TODAY" \
    --data-urlencode "loyer_nu=150000" \
    --data-urlencode "loyer_contractuel=150000" \
    --data-urlencode "charges_mensuelles=0" \
    --data-urlencode "caution=150000" \
    --data-urlencode "type_bail=habitation" \
    --data-urlencode "taux_commission_applique=10" \
    --data-urlencode "frais_agence=150000" \
    --data-urlencode "nombre_mois_caution=1" \
    -H "Content-Type: application/x-www-form-urlencoded")
  assert "Creer contrat valide -- succes (302)" "$resp_contrat" "^302$" "$resp_contrat"
else
  warn "Pas de bien disponible ou locataire libre -- contrat valide non teste"
fi

resp=$(get_page "$BASE/admin/contrats/1" "$COOKIEFILE_ADMIN")
code=$(http_code_of "$\1")
assert "Detail contrat #1 accessible (200)" "$code" "^200$" "$code"

# ============================================================
# MODULE 5 — QUITTANCES / PAIEMENTS
# ============================================================
section "MODULE 5 -- Quittances / Paiements"

resp=$(get_page "$BASE/admin/paiements" "$COOKIEFILE_ADMIN")
code=$(http_code_of "$\1")
assert "Liste paiements accessible (200)" "$code" "^200$" "$code"

resp=$(get_page "$BASE/admin/paiements/create" "$COOKIEFILE_ADMIN")
code=$(http_code_of "$\1")
assert "Formulaire creation paiement accessible (200)" "$code" "^200$" "$code"

resp=$(get_page "$BASE/admin/paiements/1" "$COOKIEFILE_ADMIN")
code=$(http_code_of "$\1")
assert "Detail paiement #1 accessible (200)" "$code" "^200$" "$code"

resp_pdf=$(curl -sb "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}|%{content_type}" "$BASE/admin/paiements/1/pdf")
code_pdf=$(echo "$resp_pdf" | cut -d'|' -f1)
ctype_pdf=$(echo "$resp_pdf" | cut -d'|' -f2)
assert "PDF quittance #1 -- HTTP 200" "$code_pdf" "^200$" "$code_pdf"
assert "PDF quittance #1 -- Content-Type PDF" "$ctype_pdf" "pdf" "$ctype_pdf"

CSRF_PAY=$(get_csrf "$COOKIEFILE_ADMIN" "$BASE/admin/paiements/create")
resp_pay_bad=$(curl -sb "$COOKIEFILE_ADMIN" -c "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" \
  -X POST "$BASE/admin/paiements" \
  --data-urlencode "_token=$CSRF_PAY" \
  -H "Content-Type: application/x-www-form-urlencoded")
assert "Creer paiement vide -- validation (302/422)" "$resp_pay_bad" "^(302|422)$" "$resp_pay_bad"

CONTRAT_ID=$(tinker_get "echo App\Models\Contrat::where('statut','actif')->first()->id ?? '';")
if [ -n "$CONTRAT_ID" ]; then
  MOIS=$(date +%Y-%m-01)
  CSRF_PAY2=$(get_csrf "$COOKIEFILE_ADMIN" "$BASE/admin/paiements/create")
  resp_pay=$(curl -sb "$COOKIEFILE_ADMIN" -c "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" \
    -X POST "$BASE/admin/paiements" \
    --data-urlencode "_token=$CSRF_PAY2" \
    --data-urlencode "contrat_id=$CONTRAT_ID" \
    --data-urlencode "periode=$MOIS" \
    --data-urlencode "mode_paiement=wave" \
    --data-urlencode "loyer_nu=200000" \
    --data-urlencode "charges_amount=0" \
    --data-urlencode "montant_encaisse=200000" \
    -H "Content-Type: application/x-www-form-urlencoded")
  assert "Creer paiement valide -- succes ou doublon (200/302/422)" "$resp_pay" "^(200|302|422)$" "$resp_pay"
else
  warn "Aucun contrat actif -- creation paiement non testee"
fi

# ============================================================
# MODULE 6 — TABLEAU DE BORD ET SECURITE
# ============================================================
section "MODULE 6 -- Tableau de bord et securite"

resp=$(get_page "$BASE/admin/dashboard" "$COOKIEFILE_ADMIN")
body=$(echo "$resp" | sed 's/|||[0-9]*$//')
code=$(http_code_of "$\1")
assert "Dashboard admin charge (200)" "$code" "^200$" "$code"
assert "Dashboard contient des chiffres/stats" "$body" "[0-9]" "(aucun chiffre)"

rm -f "$COOKIEFILE_SA"
login_user "superadmin@bimo-tech.sn" "oui" "$COOKIEFILE_SA" > /dev/null
resp_sa=$(get_page "$BASE/superadmin/dashboard" "$COOKIEFILE_SA")
code_sa=$(http_code_of "$\1")
assert "Dashboard SuperAdmin accessible (200)" "$code_sa" "^200$" "$code_sa"

resp_unauth=$(curl -s -o /dev/null -w "%{http_code}" "$BASE/admin/dashboard")
assert "Dashboard sans auth -- redirection (302)" "$resp_unauth" "^302$" "$resp_unauth"

rm -f "$COOKIEFILE_LOC"
login_user "elhadjimalickaidara69@gmail.com" "password" "$COOKIEFILE_LOC" > /dev/null
resp_loc=$(get_page "$BASE/locataire/dashboard" "$COOKIEFILE_LOC")
code_loc=$(http_code_of "$\1")
assert "Dashboard locataire accessible (200/302)" "$code_loc" "^(200|302)$" "$code_loc"

resp_loc_admin=$(curl -sb "$COOKIEFILE_LOC" -s -o /dev/null -w "%{http_code}" "$BASE/admin/biens")
assert "Locataire bloque sur route admin (302/403)" "$resp_loc_admin" "^(302|403)$" "$resp_loc_admin"

resp_rapport=$(get_page "$BASE/admin/rapports/financier" "$COOKIEFILE_ADMIN")
code_rapport=$(http_code_of "$\1")
assert "Rapport financier accessible (200)" "$code_rapport" "^200$" "$code_rapport"

resp_imp=$(get_page "$BASE/admin/impayes" "$COOKIEFILE_ADMIN")
code_imp=$(http_code_of "$\1")
assert "Liste impayes accessible (200)" "$code_imp" "^200$" "$code_imp"

# ============================================================
# MODULE BONUS — LOGOUT
# ============================================================
section "MODULE Bonus -- Logout"

CSRF_LOGOUT=$(get_csrf "$COOKIEFILE_ADMIN" "$BASE/admin/dashboard")
resp_logout=$(curl -sb "$COOKIEFILE_ADMIN" -c "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" \
  -X POST "$BASE/logout" \
  --data-urlencode "_token=$CSRF_LOGOUT" \
  -H "Content-Type: application/x-www-form-urlencoded")
assert "Logout -- redirection (302)" "$resp_logout" "^302$" "$resp_logout"

resp_after_logout=$(curl -sb "$COOKIEFILE_ADMIN" -s -o /dev/null -w "%{http_code}" "$BASE/admin/dashboard")
assert "Apres logout -- dashboard inaccessible (302)" "$resp_after_logout" "^302$" "$resp_after_logout"

# ============================================================
# RAPPORT FINAL
# ============================================================
echo ""
echo "========================================================"
echo "  RAPPORT DE TESTS -- BIMO-Tech"
echo "  $(date '+%Y-%m-%d %H:%M:%S')"
echo "========================================================"
for r in "${RESULTS[@]}"; do echo "$r"; done
echo ""
echo "--------------------------------------------------------"
TOTAL=$((PASS+FAIL))
echo "  TOTAL : $TOTAL tests  |  OK: $PASS  |  KO: $FAIL  |  WARN: $WARN"
echo "========================================================"

rm -f bimo_admin_cookies.txt bimo_sa_cookies.txt bimo_loc_cookies.txt bimo_bad_cookies.txt bimo_empty_cookies.txt

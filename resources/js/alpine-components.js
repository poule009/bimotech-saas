/**
 * Composants Alpine enregistrés (build CSP-safe).
 *
 * La build @alpinejs/csp n'autorise AUCUNE expression inline dans le HTML
 * (pas de `show = !show`, pas de ternaire, pas de `fetch(...)`). Toute la
 * logique vit ici, dans des composants référencés par `x-data="nom"`.
 * Les directives ne font plus que de l'accès propriété / appel de méthode.
 */
export default function registerComponents(Alpine) {
    // Affiche/masque un champ mot de passe (login + invitation collaborateur)
    Alpine.data('passwordToggle', () => ({
        show: false,
        toggle() { this.show = !this.show },
        get hidden() { return !this.show },
        get type() { return this.show ? 'text' : 'password' },
        get label() { return this.show ? 'Masquer le mot de passe' : 'Afficher le mot de passe' },
    }));

    // Formulaire de connexion — état "envoi en cours"
    Alpine.data('loginForm', () => ({
        loading: false,
        start() { this.loading = true },
        get label() { return this.loading ? 'Connexion en cours…' : 'Se connecter' },
    }));

    // Message flash auto-dismiss (délai lu via data-timeout, 0 = jamais)
    Alpine.data('flashMessage', () => ({
        show: true,
        init() {
            const t = parseInt(this.$el.dataset.timeout || '0', 10);
            if (t > 0) setTimeout(() => { this.show = false }, t);
        },
        dismiss() { this.show = false },
    }));

    // Recherche globale — overlay plein écran (mobile)
    Alpine.data('mobileSearch', () => ({
        q: '', results: [], loading: false, searchOpen: false, timer: null,
        get noResults() { return !this.loading && this.q.length > 1 && this.results.length === 0 },
        openPanel() { this.searchOpen = true; this.$nextTick(() => this.$refs.mSearch && this.$refs.mSearch.focus()) },
        close() { this.searchOpen = false; this.q = ''; this.results = [] },
        search() {
            clearTimeout(this.timer);
            if (this.q.length > 1) {
                this.loading = true;
                this.timer = setTimeout(() => {
                    fetch(this.$el.dataset.searchUrl + '?q=' + encodeURIComponent(this.q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => r.json())
                        .then(d => { this.results = d.results || []; this.loading = false })
                        .catch(() => { this.loading = false });
                }, 250);
            } else { this.results = []; this.loading = false }
        },
    }));

    // Recherche globale — dropdown (desktop)
    Alpine.data('desktopSearch', () => ({
        q: '', results: [], show: false, timer: null,
        get hasResults() { return this.show && this.results.length > 0 },
        hide() { this.show = false; this.q = '' },
        hideKeepQuery() { this.show = false },
        search() {
            clearTimeout(this.timer);
            if (this.q.length > 1) {
                this.timer = setTimeout(() => {
                    fetch(this.$el.dataset.searchUrl + '?q=' + encodeURIComponent(this.q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(r => r.json())
                        .then(d => { this.results = d.results || []; this.show = true });
                }, 250);
            } else { this.show = false }
        },
    }));

    // Formulaire de filtres — soumet le formulaire quand un select change (CSP-safe)
    Alpine.data('autoSubmit', () => ({
        submit() { this.$root.submit() },
    }));

    // Comptabilité — sous-nav à 3 onglets (Vue d'ensemble / Propriétaires / Agence).
    // Les classes actives/inactives sont fournies via data-* (écrites en Blade,
    // donc scannées par Tailwind) pour rester CSP-safe ET JIT-safe.
    Alpine.data('comptaTabs', () => ({
        tab: 'apercu',
        activeCls: '',
        inactiveCls: '',
        init() {
            this.activeCls   = this.$el.dataset.activeClass || '';
            this.inactiveCls = this.$el.dataset.inactiveClass || '';
            if (this.$el.dataset.default) this.tab = this.$el.dataset.default;
        },
        selectApercu()  { this.tab = 'apercu' },
        selectProprio() { this.tab = 'proprietaires' },
        selectAgence()  { this.tab = 'agence' },
        get showApercu()  { return this.tab === 'apercu' },
        get showProprio() { return this.tab === 'proprietaires' },
        get showAgence()  { return this.tab === 'agence' },
        get apercuClass()  { return this.tab === 'apercu' ? this.activeCls : this.inactiveCls },
        get proprioClass() { return this.tab === 'proprietaires' ? this.activeCls : this.inactiveCls },
        get agenceClass()  { return this.tab === 'agence' ? this.activeCls : this.inactiveCls },
    }));

    // Comptabilité — ligne propriétaire dépliable (détail par bien)
    Alpine.data('detailToggle', () => ({
        open: false,
        toggle() { this.open = !this.open },
    }));

    // Comptabilité — formulaire « ajouter une dépense » (propriétaire ou agence)
    Alpine.data('expenseForm', () => ({
        open: false,
        choice: 'owner',
        paiementId: '',
        ownerActionBase: '',
        selCls: '',
        unselCls: '',
        init() {
            this.ownerActionBase = this.$el.dataset.ownerActionBase || '';
            this.selCls   = this.$el.dataset.selectedClass || '';
            this.unselCls = this.$el.dataset.unselectedClass || '';
            if (this.$el.dataset.openInit) this.open = true;
        },
        toggle() { this.open = !this.open },
        hide()   { this.open = false },
        chooseOwner()  { this.choice = 'owner' },
        chooseAgency() { this.choice = 'agency' },
        get isOwner()  { return this.choice === 'owner' },
        get isAgency() { return this.choice === 'agency' },
        get ownerCardClass()  { return this.choice === 'owner' ? this.selCls : this.unselCls },
        get agencyCardClass() { return this.choice === 'agency' ? this.selCls : this.unselCls },
        // URL de POST de la dépense propriétaire, calculée d'après le paiement choisi
        get ownerFormAction() { return this.ownerActionBase.replace('__PID__', this.paiementId) },
    }));
}

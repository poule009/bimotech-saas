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
}

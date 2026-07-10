/**
 * Composants Alpine — build CSP (@alpinejs/csp).
 * Aucune expression inline dans les vues : uniquement des références à des
 * propriétés / getters / méthodes déclarés ici.
 */
export function registerComponents(Alpine) {
    // Ouvre / ferme la sidebar sur mobile (off-canvas). Desktop = toujours visible.
    Alpine.data('sidebar', () => ({
        open: false,
        get panelClass() {
            return this.open ? 'translate-x-0' : '-translate-x-full';
        },
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        },
    }));

    // Champ générique « Rechercher-ou-Créer » (combobox + création rapide inline).
    // Config lue sur data-attributes : data-search-url, data-create-url, data-type,
    // et éventuellement data-selected-id/name/sub (pré-sélection en édition).
    Alpine.data('searchOrCreate', () => ({
        query: '',
        results: [],
        loading: false,
        open: false,
        creating: false,
        createName: '',
        createPhone: '',
        createError: '',
        submitting: false,
        searchUrl: '',
        createUrl: '',
        type: 'élément',
        selectedId: '',
        selectedName: '',
        selectedSub: '',
        selectedInitials: '',
        allowCreate: true,
        fillField: '',
        _timer: null,

        init() {
            const d = this.$el.dataset;
            this.searchUrl = d.searchUrl || '';
            this.createUrl = d.createUrl || '';
            this.type = d.type || 'élément';
            this.allowCreate = d.allowCreate !== 'false';
            this.fillField = d.fillField || '';
            if (d.selectedId) {
                this.selectedId = d.selectedId;
                this.selectedName = d.selectedName || '';
                this.selectedSub = d.selectedSub || '';
                this.selectedInitials = this._initials(this.selectedName);
            }
        },

        get hasSelected() { return this.selectedId !== '' && this.selectedId !== null; },
        get showInput() { return !this.hasSelected && !this.creating; },
        get showDropdown() { return this.open && !this.creating && !this.hasSelected; },
        get showCreating() { return this.creating; },
        get noResults() { return !this.loading && this.results.length === 0; },
        get showCreateRow() { return this.allowCreate; },
        get createLabel() {
            return this.query.trim() === ''
                ? 'Créer un nouveau ' + this.type
                : 'Créer « ' + this.query.trim() + ' » comme nouveau ' + this.type;
        },
        get emptyLabel() {
            return this.query.trim() === ''
                ? 'Aucun ' + this.type + ' disponible'
                : 'Aucun ' + this.type + ' trouvé pour « ' + this.query.trim() + ' »';
        },

        // Au focus : ouvre la liste et affiche d'emblée les éléments disponibles (recherche à vide).
        onFocus() {
            this.open = true;
            if (this.results.length === 0 && ! this.loading) {
                this.loading = true;
                this.runSearch();
            }
        },

        closeList() { this.open = false; },

        onInput(event) {
            if (event && event.target) { this.query = event.target.value; }
            this.open = true;
            this.loading = true;
            clearTimeout(this._timer);
            this._timer = setTimeout(() => this.runSearch(), 300);
        },

        runSearch() {
            const sep = this.searchUrl.includes('?') ? '&' : '?';
            const url = this.searchUrl + sep + 'q=' + encodeURIComponent(this.query.trim());
            fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            })
                .then((r) => (r.ok ? r.json() : []))
                .then((data) => { this.results = Array.isArray(data) ? data : []; this.loading = false; })
                .catch(() => { this.results = []; this.loading = false; });
        },

        pick(event) {
            const id = event.currentTarget.dataset.id;
            const item = this.results.find((r) => String(r.id) === String(id));
            if (item) { this.choose(item); }
        },

        choose(item) {
            this.selectedId = item.id;
            this.selectedName = item.name;
            this.selectedSub = item.sub || '';
            this.selectedInitials = this._initials(item.name);
            this.open = false;
            this.results = [];
            this.creating = false;
            this.query = '';
            // Pré-remplit un champ cible (ex. loyer depuis le bien) si fourni.
            if (this.fillField && item.fill != null && item.fill !== '') {
                const el = document.getElementById(this.fillField);
                if (el) { el.value = item.fill; }
            }
        },

        clear() {
            this.selectedId = '';
            this.selectedName = '';
            this.selectedSub = '';
            this.query = '';
            this.results = [];
        },

        startCreate() {
            this.creating = true;
            this.createName = this.query.trim();
            this.createPhone = '';
            this.createError = '';
            this.open = false;
        },

        cancelCreate() { this.creating = false; },

        submitCreate() {
            if (this.submitting) { return; }
            this.createError = '';
            if (this.createName.trim() === '') { this.createError = 'Le nom est obligatoire.'; return; }
            this.submitting = true;
            const token = document.querySelector('meta[name="csrf-token"]');
            fetch(this.createUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token ? token.content : '',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ name: this.createName.trim(), telephone: this.createPhone.trim() }),
            })
                .then((r) => r.json().then((data) => ({ ok: r.ok, data })))
                .then(({ ok, data }) => {
                    this.submitting = false;
                    if (!ok) { this.createError = (data && data.message) || 'Création impossible.'; return; }
                    this.choose(data);
                })
                .catch(() => { this.submitting = false; this.createError = 'Erreur réseau.'; });
        },

        _initials(name) { return (name || '').trim().substring(0, 2).toUpperCase(); },
    }));

    // Formulaire de création d'immeuble : génération d'appartements (simple / par étage).
    Alpine.data('immeubleForm', () => ({
        avecUnites: true,
        mode: 'simple',
        avecRdc: true,
        rdcDifferent: false,
        get avecUnitesValue() { return this.avecUnites ? '1' : '0'; },
        get modeValue() { return this.mode; },
        get avecRdcValue() { return this.avecRdc ? '1' : '0'; },
        get rdcDifferentValue() { return this.rdcDifferent ? '1' : '0'; },
        get showUnites() { return this.avecUnites; },
        get showSimple() { return this.avecUnites && this.mode === 'simple'; },
        get showEtage() { return this.avecUnites && this.mode === 'etage'; },
        get showRdc() { return this.avecUnites && this.mode === 'etage' && this.avecRdc; },
        get showRdcDiff() { return this.showRdc && this.rdcDifferent; },
        get unitesSwitchClass() { return this.avecUnites ? 'bg-teal' : 'bg-line'; },
        get unitesKnobClass() { return this.avecUnites ? 'left-[20px]' : 'left-[3px]'; },
        get simpleTabClass() { return this.mode === 'simple' ? 'bg-teal text-paper' : 'text-muted'; },
        get etageTabClass() { return this.mode === 'etage' ? 'bg-teal text-paper' : 'text-muted'; },
        get rdcSwitchClass() { return this.avecRdc ? 'bg-teal' : 'bg-line'; },
        get rdcKnobClass() { return this.avecRdc ? 'left-[20px]' : 'left-[3px]'; },
        get rdcDiffSwitchClass() { return this.rdcDifferent ? 'bg-teal' : 'bg-line'; },
        get rdcDiffKnobClass() { return this.rdcDifferent ? 'left-[20px]' : 'left-[3px]'; },
        toggleUnites() { this.avecUnites = !this.avecUnites; },
        setSimple() { this.mode = 'simple'; },
        setEtage() { this.mode = 'etage'; },
        toggleRdc() { this.avecRdc = !this.avecRdc; },
        toggleRdcDiff() { this.rdcDifferent = !this.rdcDifferent; },
    }));

    // Formulaire contrat : convertit « durée » (12/24/indéterminée) en date_fin.
    Alpine.data('contratDuree', () => ({
        debut: '',
        duree: '12',
        init() {
            this.debut = this.$el.dataset.debut || '';
            if (this.$el.dataset.duree) { this.duree = this.$el.dataset.duree; }
        },
        get dateFin() {
            if (this.duree === 'indeterminee' || ! this.debut) { return ''; }
            const d = new Date(this.debut);
            if (isNaN(d.getTime())) { return ''; }
            d.setMonth(d.getMonth() + parseInt(this.duree, 10));
            return d.toISOString().slice(0, 10);
        },
    }));

    // Paramètres agence : onglets (Identité / Fiscalité / Documents) + toggle TVA agence.
    Alpine.data('settingsForm', () => ({
        tab: 'identite',
        tva: false,
        init() { this.tva = this.$el.dataset.tva === '1'; },
        get isIdentite() { return this.tab === 'identite'; },
        get isFiscalite() { return this.tab === 'fiscalite'; },
        get isDocuments() { return this.tab === 'documents'; },
        get identiteTabClass() { return this.tab === 'identite' ? 'text-teal border-teal' : 'text-muted border-transparent'; },
        get fiscaliteTabClass() { return this.tab === 'fiscalite' ? 'text-teal border-teal' : 'text-muted border-transparent'; },
        get documentsTabClass() { return this.tab === 'documents' ? 'text-teal border-teal' : 'text-muted border-transparent'; },
        get tvaValue() { return this.tva ? '1' : '0'; },
        get tvaSwitchClass() { return this.tva ? 'bg-teal' : 'bg-line'; },
        get tvaKnobClass() { return this.tva ? 'left-[20px]' : 'left-[3px]'; },
        showIdentite() { this.tab = 'identite'; },
        showFiscalite() { this.tab = 'fiscalite'; },
        showDocuments() { this.tab = 'documents'; },
        toggleTva() { this.tva = !this.tva; },
    }));

    // Confirmation avant soumission d'un formulaire (data-confirm sur le <form>).
    Alpine.data('confirmForm', () => ({
        submit(event) {
            if (! window.confirm(this.$el.dataset.confirm || 'Confirmer ?')) {
                event.preventDefault();
            }
        },
    }));

    // Groupe de gravité repliable (module Quittances) — ouvert/fermé selon data-open.
    Alpine.data('severityGroup', () => ({
        open: true,
        init() { this.open = this.$el.dataset.open !== 'false'; },
        get chevClass() { return this.open ? '' : '-rotate-90'; },
        toggle() { this.open = !this.open; },
    }));

    // Section repliable (accordéon).
    Alpine.data('collapsible', () => ({
        open: false,
        get chevClass() { return this.open ? 'rotate-180' : ''; },
        toggle() { this.open = !this.open; },
    }));

    // Module Comptabilité — onglets principaux (Propriétaires / Agence / Vérification).
    Alpine.data('comptaTabs', () => ({
        tab: 'proprietaires',
        init() { if (this.$el.dataset.tab) { this.tab = this.$el.dataset.tab; } },
        _cls(name) { return this.tab === name ? 'text-teal border-teal' : 'text-muted border-transparent'; },
        get isProprietaires() { return this.tab === 'proprietaires'; },
        get isAgence() { return this.tab === 'agence'; },
        get isVerification() { return this.tab === 'verification'; },
        get proprietairesTabClass() { return this._cls('proprietaires'); },
        get agenceTabClass() { return this._cls('agence'); },
        get verificationTabClass() { return this._cls('verification'); },
        showProprietaires() { this.tab = 'proprietaires'; },
        showAgence() { this.tab = 'agence'; },
        showVerification() { this.tab = 'verification'; },
    }));

    // Vérification : compare le solde théorique (argent des tiers) au montant réel saisi.
    Alpine.data('verification', () => ({
        theorique: 0,
        reel: '',
        init() { this.theorique = parseFloat(this.$el.dataset.theorique || '0') || 0; },
        get reelNum() {
            const n = parseFloat(String(this.reel).replace(/\s/g, '').replace(',', '.'));
            return isNaN(n) ? null : n;
        },
        get checked() { return this.reelNum !== null && String(this.reel).trim() !== ''; },
        get ecart() { return this.reelNum === null ? 0 : (this.reelNum - this.theorique); },
        get equilibre() { return this.checked && Math.abs(this.ecart) < 1; },
        get ecartAbs() {
            const v = Math.abs(this.ecart);
            return v.toLocaleString('fr-FR', { maximumFractionDigits: 0 }) + ' F';
        },
        get manquant() { return this.ecart < 0; },
        get showResult() { return this.checked; },
        get showOk() { return this.checked && this.equilibre; },
        get showEcart() { return this.checked && !this.equilibre; },
        get ecartLabel() { return this.manquant ? 'Manque sur le compte' : 'Excédent sur le compte'; },
    }));

    // Formulaire « ajouter une dépense » propriétaire : fourche liée-à-un-bien / directe.
    Alpine.data('depenseProprioForm', () => ({
        open: false,
        type: 'bien',
        get show() { return this.open; },
        get isBien() { return this.type === 'bien'; },
        get isDirect() { return this.type === 'direct'; },
        get typeValue() { return this.type; },
        get bienTabClass() { return this.type === 'bien' ? 'bg-teal text-paper' : 'text-muted'; },
        get directTabClass() { return this.type === 'direct' ? 'bg-teal text-paper' : 'text-muted'; },
        toggle() { this.open = !this.open; },
        close() { this.open = false; },
        setBien() { this.type = 'bien'; },
        setDirect() { this.type = 'direct'; },
    }));

    // Bascule d'affichage grille / liste (module Biens).
    Alpine.data('viewToggle', () => ({
        mode: 'grid',
        get isGrid() { return this.mode === 'grid'; },
        get isList() { return this.mode === 'list'; },
        get gridBtnClass() { return this.mode === 'grid' ? 'bg-teal text-paper' : 'text-muted'; },
        get listBtnClass() { return this.mode === 'list' ? 'bg-teal text-paper' : 'text-muted'; },
        setGrid() { this.mode = 'grid'; },
        setList() { this.mode = 'list'; },
    }));

    // Onglets d'une fiche (Informations / Biens / Documents).
    Alpine.data('tabs', (initial = 'info') => ({
        active: initial,
        _cls(name) { return this.active === name ? 'text-teal border-teal' : 'text-muted border-transparent'; },
        get isInfo() { return this.active === 'info'; },
        get isBiens() { return this.active === 'biens'; },
        get isContrats() { return this.active === 'contrats'; },
        get isGarant() { return this.active === 'garant'; },
        get isDocs() { return this.active === 'docs'; },
        get infoTabClass() { return this._cls('info'); },
        get biensTabClass() { return this._cls('biens'); },
        get contratsTabClass() { return this._cls('contrats'); },
        get garantTabClass() { return this._cls('garant'); },
        get docsTabClass() { return this._cls('docs'); },
        showInfo() { this.active = 'info'; },
        showBiens() { this.active = 'biens'; },
        showContrats() { this.active = 'contrats'; },
        showGarant() { this.active = 'garant'; },
        showDocs() { this.active = 'docs'; },
    }));

    // Formulaire locataire : fork Particulier / Bureau-Société.
    Alpine.data('tenantForm', () => ({
        type: 'particulier',
        init() { if (this.$el.dataset.tenantType) this.type = this.$el.dataset.tenantType; },
        get isParticulier() { return this.type === 'particulier'; },
        get isEntreprise() { return this.type === 'entreprise'; },
        get typeValue() { return this.type; },
        get estEntrepriseValue() { return this.type === 'entreprise' ? '1' : '0'; },
        get forkParticulierClass() { return this.type === 'particulier' ? 'border-teal bg-white shadow-sm' : 'border-line bg-paper'; },
        get forkEntrepriseClass() { return this.type === 'entreprise' ? 'border-teal bg-white shadow-sm' : 'border-line bg-paper'; },
        setParticulier() { this.type = 'particulier'; },
        setEntreprise() { this.type = 'entreprise'; },
    }));

    // Formulaire propriétaire : fourche particulier/entreprise + toggle TVA.
    // L'état initial est lu sur les data-attributes du <form> (édition + reprise après erreur).
    Alpine.data('ownerForm', () => ({
        type: 'particulier',
        tva: false,
        init() {
            if (this.$el.dataset.ownerType) {
                this.type = this.$el.dataset.ownerType;
            }
            this.tva = this.$el.dataset.ownerTva === '1';
        },
        get isEntreprise() { return this.type === 'entreprise'; },
        get isParticulier() { return this.type === 'particulier'; },
        get moraleValue() { return this.type === 'entreprise' ? '1' : '0'; },
        get tvaValue() { return this.tva ? '1' : '0'; },
        get forkParticulierClass() {
            return this.type === 'particulier' ? 'border-teal bg-white shadow-sm' : 'border-line bg-paper';
        },
        get forkEntrepriseClass() {
            return this.type === 'entreprise' ? 'border-teal bg-white shadow-sm' : 'border-line bg-paper';
        },
        get tvaSwitchClass() { return this.tva ? 'bg-teal' : 'bg-line'; },
        get tvaKnobClass() { return this.tva ? 'left-[20px]' : 'left-[3px]'; },
        setParticulier() { this.type = 'particulier'; },
        setEntreprise() { this.type = 'entreprise'; },
        toggleTva() { this.tva = !this.tva; },
    }));

    // Affiche / masque un champ mot de passe.
    Alpine.data('passwordToggle', () => ({
        show: false,
        get type() {
            return this.show ? 'text' : 'password';
        },
        get label() {
            return this.show ? 'Masquer' : 'Afficher';
        },
        toggle() {
            this.show = !this.show;
        },
    }));

    // Module Import — panneau latéral d'aide (ouvrable depuis n'importe quelle étape).
    Alpine.data('importDrawer', () => ({
        open: false,
        openPanel() { this.open = true; },
        closePanel() { this.open = false; },
        get overlayClass() { return this.open ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'; },
        get panelClass() { return this.open ? 'translate-x-0' : 'translate-x-full'; },
    }));

    // Module Import — zone d'upload : au choix d'un fichier, affiche le nom et
    // soumet immédiatement le formulaire (déclenche l'aperçu côté serveur).
    Alpine.data('importUpload', () => ({
        filename: '',
        submitting: false,
        get label() { return this.filename || 'Cliquez pour choisir votre fichier rempli'; },
        get idle() { return ! this.submitting; },
        pick(event) {
            const file = event.target.files && event.target.files[0];
            if (! file) return;
            this.filename = file.name;
            this.submitting = true;
            this.$refs.form.submit();
        },
    }));
}

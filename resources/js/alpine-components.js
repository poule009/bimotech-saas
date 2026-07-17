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
            // Notifie le formulaire parent (ex. aperçu fiscal du contrat) — sans effet si rien n'écoute.
            this.$el.dispatchEvent(new CustomEvent('soc:chosen', { bubbles: true, detail: item }));
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

    // Formulaire contrat enrichi : durée (→ date_fin) + APERÇU FISCAL en direct.
    // L'aperçu appelle un endpoint serveur (data-apercu-url) qui réutilise
    // FiscalService — aucune règle de TVA n'est dupliquée côté JS.
    Alpine.data('contratForm', () => ({
        // — durée —
        debut: '',
        duree: '12',
        // — état formulaire fiscal —
        loyer: '',
        charges: '',
        tom: '',
        mode: 'debours',
        meuble: false,
        // — aperçu —
        apercuUrl: '',
        loading: false,
        ready: false,
        r: null,
        _timer: null,

        init() {
            this.debut = this.$el.dataset.debut || '';
            if (this.$el.dataset.duree) { this.duree = this.$el.dataset.duree; }
            this.apercuUrl = this.$el.dataset.apercuUrl || '';
            // Reprend les valeurs rendues côté serveur (prefill / old()).
            this.loyer = this._dom('loyer_nu');
            this.charges = this._dom('charges_mensuelles');
            this.tom = this._dom('tom_amount');
            const m = this.$el.querySelector('[name="mode_facturation_charges"]');
            if (m && m.value) { this.mode = m.value; }
            // Réactivité fiable : recalcule l'aperçu dès qu'une donnée fiscale change.
            // ($watch garantit l'exécution sur le proxy réactif — plus robuste que
            //  x-on:input seul, dont le `this` différé n'était pas toujours réactif.)
            this.$watch('loyer',   () => this.scheduleRefresh());
            this.$watch('charges', () => this.scheduleRefresh());
            this.$watch('tom',     () => this.scheduleRefresh());
            this.$watch('mode',    () => this.scheduleRefresh());
            this.$nextTick(() => this.scheduleRefresh());
        },

        _dom(name) {
            const el = this.$el.querySelector('[name="' + name + '"]');
            return el ? el.value : '';
        },

        // — durée —
        get dateFin() {
            if (this.duree === 'indeterminee' || ! this.debut) { return ''; }
            const d = new Date(this.debut);
            if (isNaN(d.getTime())) { return ''; }
            d.setMonth(d.getMonth() + parseInt(this.duree, 10));
            return d.toISOString().slice(0, 10);
        },

        // — charges / mode —
        get chargesNum() {
            const n = parseFloat(String(this.charges).replace(/\s/g, '').replace(',', '.'));
            return isNaN(n) ? 0 : n;
        },
        get showChargesMode() { return this.chargesNum > 0; },

        // — sélection du bien (événement soc:chosen émis par searchOrCreate) —
        onBienChosen(event) {
            const item = event.detail || {};
            if (item.fill != null) { this.loyer = String(item.fill); }
            if (item.tom != null && (this.tom === '' || Number(this.tom) === 0)) {
                this.tom = String(item.tom);
            }
            if (typeof item.meuble !== 'undefined') { this.meuble = !!item.meuble; }
            this.scheduleRefresh();
        },

        onInput() { this.scheduleRefresh(); },

        scheduleRefresh() {
            // self = proxy réactif Alpine, conservé à travers le setTimeout
            const self = this;
            clearTimeout(self._timer);
            self._timer = setTimeout(() => self.refresh(), 350);
        },

        refresh() {
            // self garantit que les affectations async (fetch.then) restent réactives.
            const self = this;
            const bienEl = self.$el.querySelector('[name="bien_id"]');
            const bienId = bienEl ? bienEl.value : '';
            const loyerNum = parseFloat(String(self.loyer).replace(/\s/g, '').replace(',', '.'));
            if (! bienId || ! loyerNum || loyerNum <= 0 || ! self.apercuUrl) {
                self.ready = false; self.r = null; return;
            }
            self.loading = true;
            const typeEl = self.$el.querySelector('[name="type_bail"]');
            const token = document.querySelector('meta[name="csrf-token"]');
            fetch(self.apercuUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token ? token.content : '',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    bien_id: bienId,
                    loyer_nu: loyerNum,
                    charges_mensuelles: self.chargesNum,
                    tom_amount: self.tom,
                    mode_facturation_charges: self.showChargesMode ? self.mode : null,
                    type_bail: typeEl ? typeEl.value : 'habitation',
                }),
            })
                .then((res) => (res.ok ? res.json() : null))
                .then((data) => {
                    self.loading = false;
                    if (data && data.ok) { self.r = data; self.ready = true; }
                    else { self.r = null; self.ready = false; }
                })
                .catch(() => { self.loading = false; self.ready = false; });
        },

        _fmt(n) {
            const v = Math.round(Number(n) || 0);
            return v.toLocaleString('fr-FR').replace(/[ ,]/g, ' ') + ' F';
        },

        // — getters d'affichage —
        get show() { return this.ready && !! this.r; },
        get isLoading() { return this.loading; },
        get tauxTvaLabel() { return (this.r ? this.r.taux_tva_loyer : 0) + ' %'; },
        get exonere() { return !! (this.r && ! this.r.loyer_assujetti); },
        get loyerHtTxt() { return this._fmt(this.r && this.r.loyer_ht); },
        get tvaLoyerTxt() { return this._fmt(this.r && this.r.tva_loyer); },
        get loyerTtcTxt() { return this._fmt(this.r && this.r.loyer_ttc); },
        get chargesTxt() { return this._fmt(this.r && this.r.charges); },
        get tvaChargesTxt() { return this._fmt(this.r && this.r.tva_charges); },
        get tomTxt() { return this._fmt(this.r && this.r.tom); },
        get encaisseTxt() { return this._fmt(this.r && this.r.montant_encaisse); },
        get commTtcTxt() { return this._fmt(this.r && this.r.commission_ttc); },
        get tvaCommTxt() { return this._fmt(this.r && this.r.tva_commission); },
        get netTxt() { return this._fmt(this.r && this.r.net_a_verser); },
        get hasCharges() { return !! (this.r && Number(this.r.charges) > 0); },
        get hasTvaCharges() { return !! (this.r && Number(this.r.tva_charges) > 0); },
        get hasTom() { return !! (this.r && Number(this.r.tom) > 0); },
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

    // Durée écoulée d'une session d'impersonation active (Support / Debug).
    // data-started = timestamp Unix (secondes). Se met à jour chaque seconde.
    Alpine.data('impersonationTimer', () => ({
        label: '',
        _timer: null,
        init() {
            this.tick();
            this._timer = setInterval(() => this.tick(), 1000);
        },
        destroy() { clearInterval(this._timer); },
        tick() {
            const started = parseInt(this.$el.dataset.started || '0', 10);
            let s = Math.max(0, Math.floor(Date.now() / 1000) - started);
            const h = Math.floor(s / 3600);
            const m = Math.floor((s % 3600) / 60);
            if (h > 0) {
                this.label = `${h} h ${String(m).padStart(2, '0')}`;
            } else if (m > 0) {
                this.label = `${m} min`;
            } else {
                this.label = `${s} s`;
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

    // Super Admin — fiche agence : onglets (Infos / Abonnement / Usage / Activité).
    Alpine.data('agencyTabs', () => ({
        active: 'infos',
        init() { if (this.$el.dataset.tab) { this.active = this.$el.dataset.tab; } },
        _cls(name) { return this.active === name ? 'text-teal border-gold' : 'text-muted border-transparent hover:text-ink'; },
        get isInfos() { return this.active === 'infos'; },
        get isAbo() { return this.active === 'abo'; },
        get isUsage() { return this.active === 'usage'; },
        get isActivite() { return this.active === 'activite'; },
        get infosClass() { return this._cls('infos'); },
        get aboClass() { return this._cls('abo'); },
        get usageClass() { return this._cls('usage'); },
        get activiteClass() { return this._cls('activite'); },
        showInfos() { this.active = 'infos'; },
        showAbo() { this.active = 'abo'; },
        showUsage() { this.active = 'usage'; },
        showActivite() { this.active = 'activite'; },
    }));

    // Super Admin — liste agences : recherche (≥ 3 car., débounce 300 ms) + filtres.
    // $root = le <form> porteur ; les <select> soumettent immédiatement.
    Alpine.data('agencyFilters', () => ({
        _timer: null,
        search(event) {
            const v = (event.target.value || '').trim();
            clearTimeout(this._timer);
            // On ne soumet qu'à partir de 3 caractères — ou à vide (pour réinitialiser).
            if (v.length > 0 && v.length < 3) { return; }
            this._timer = setTimeout(() => this.$root.submit(), 300);
        },
        apply() { this.$root.submit(); },
    }));

    // Formulaire de filtres qui se soumet au changement (facturation Super Admin).
    // Séparé d'agencyFilters : pas de recherche texte débouncée ici, et le champ
    // « période personnalisée » ne doit s'afficher que sur le choix « perso ».
    Alpine.data('billingFilters', (initial = 'mois') => ({
        periode: initial,
        apply() { this.$root.submit(); },
        onPeriode(event) {
            this.periode = event.target.value;
            // « perso » attend deux dates : on laisse l'utilisateur les saisir
            // avant de soumettre, sinon on filtrerait sur des bornes vides.
            if (this.periode !== 'perso') { this.$root.submit(); }
        },
        get isPerso() { return this.periode === 'perso'; },
    }));

    // Ligne de paiement en attente : déplie le champ « motif de rejet », qui est
    // obligatoire (il est affiché à l'agence) et ne tient pas dans la ligne.
    Alpine.data('rejectRow', () => ({
        open: false,
        toggle() { this.open = ! this.open; },
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
        pieceNom: '',
        pieceDefault: 'Choisir un fichier',
        init() {
            if (this.$el.dataset.tenantType) this.type = this.$el.dataset.tenantType;
            if (this.$el.dataset.pieceDefault) this.pieceDefault = this.$el.dataset.pieceDefault;
        },
        get isParticulier() { return this.type === 'particulier'; },
        get isEntreprise() { return this.type === 'entreprise'; },
        get typeValue() { return this.type; },
        get estEntrepriseValue() { return this.type === 'entreprise' ? '1' : '0'; },
        get forkParticulierClass() { return this.type === 'particulier' ? 'border-teal bg-white shadow-sm' : 'border-line bg-paper'; },
        get forkEntrepriseClass() { return this.type === 'entreprise' ? 'border-teal bg-white shadow-sm' : 'border-line bg-paper'; },
        get pieceLabel() { return this.pieceNom || this.pieceDefault; },
        pickPiece(event) { const f = event.currentTarget.files; this.pieceNom = (f && f.length) ? f[0].name : ''; },
        setParticulier() { this.type = 'particulier'; },
        setEntreprise() { this.type = 'entreprise'; },
    }));

    // Formulaire propriétaire : fourche particulier/entreprise + toggle TVA.
    // L'état initial est lu sur les data-attributes du <form> (édition + reprise après erreur).
    Alpine.data('ownerForm', () => ({
        type: 'particulier',
        tva: false,
        brsDispense: false,
        pieceNom: '',
        pieceDefault: 'Choisir un fichier',
        init() {
            if (this.$el.dataset.ownerType) {
                this.type = this.$el.dataset.ownerType;
            }
            this.tva = this.$el.dataset.ownerTva === '1';
            this.brsDispense = this.$el.dataset.ownerBrsDispense === '1';
            if (this.$el.dataset.pieceDefault) this.pieceDefault = this.$el.dataset.pieceDefault;
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
        get brsDispenseValue() { return this.brsDispense ? '1' : '0'; },
        get brsSwitchClass() { return this.brsDispense ? 'bg-gold' : 'bg-line'; },
        get brsKnobClass() { return this.brsDispense ? 'left-[20px]' : 'left-[3px]'; },
        get pieceLabel() { return this.pieceNom || this.pieceDefault; },
        pickPiece(event) { const f = event.currentTarget.files; this.pieceNom = (f && f.length) ? f[0].name : ''; },
        setParticulier() { this.type = 'particulier'; },
        setEntreprise() { this.type = 'entreprise'; },
        toggleTva() { this.tva = !this.tva; },
        toggleBrsDispense() { this.brsDispense = !this.brsDispense; },
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

    // Module Mon équipe — sélecteur de rôle de départ (invitation).
    Alpine.data('equipeRolePicker', () => ({
        preset: 'secretaire',
        init() { this.preset = this.$el.dataset.initial || 'secretaire'; },
        pick(event) { this.preset = event.currentTarget.dataset.preset; },
        _cardClass(p) {
            return this.preset === p
                ? 'border-teal bg-white shadow-sm'
                : 'border-line bg-paper hover:border-gold/60';
        },
        get adminCardClass() { return this._cardClass('administrateur'); },
        get secretaireCardClass() { return this._cardClass('secretaire'); },
        get customCardClass() { return this._cardClass('personnalise'); },
        get showAdmin() { return this.preset === 'administrateur'; },
        get showSecretaire() { return this.preset === 'secretaire'; },
        get showCustom() { return this.preset === 'personnalise'; },
    }));

    // Module Mon équipe — matrice de permissions (3 niveaux par module).
    // Impératif (CSP-safe) : l'état initial est rendu côté serveur, Alpine gère les clics.
    Alpine.data('permMatrix', () => ({
        setGroup(group, level) {
            group.querySelectorAll('button.lvl-opt').forEach((b) => {
                b.classList.remove('on-none', 'on-view', 'on-full');
                if (b.dataset.level === level) b.classList.add('on-' + level);
            });
            const input = group.querySelector('input[type=hidden]');
            if (input) input.value = level;
        },
        pick(event) {
            const btn = event.currentTarget;
            const group = btn.closest('[data-group]');
            this.setGroup(group, btn.dataset.level);
        },
        applyPreset(event) {
            const levels = JSON.parse(event.currentTarget.dataset.levels || '{}');
            this.$el.querySelectorAll('[data-group]').forEach((group) => {
                this.setGroup(group, levels[group.dataset.group] || 'none');
            });
        },
    }));

    // Module Mon profil — onglets (Identité / Sécurité / Notifications / Mes accès).
    Alpine.data('profilTabs', () => ({
        active: 'identite',
        init() {
            // Onglet initial forcé par le serveur (ex. erreur mot de passe → Sécurité),
            // sinon ancre d'URL, sinon Identité.
            const forced = this.$el.dataset.initial;
            const valid = ['identite', 'securite', 'notifications', 'acces'];
            if (valid.includes(forced)) { this.active = forced; return; }
            const h = (window.location.hash || '').replace('#', '');
            if (valid.includes(h)) this.active = h;
        },
        _cls(name) { return this.active === name ? 'text-teal border-teal' : 'text-muted border-transparent hover:text-ink'; },
        get isIdentite() { return this.active === 'identite'; },
        get isSecurite() { return this.active === 'securite'; },
        get isNotifications() { return this.active === 'notifications'; },
        get isAcces() { return this.active === 'acces'; },
        get identiteClass() { return this._cls('identite'); },
        get securiteClass() { return this._cls('securite'); },
        get notificationsClass() { return this._cls('notifications'); },
        get accesClass() { return this._cls('acces'); },
        showIdentite() { this.active = 'identite'; },
        showSecurite() { this.active = 'securite'; },
        showNotifications() { this.active = 'notifications'; },
        showAcces() { this.active = 'acces'; },
    }));

    // Champ fichier simple : affiche le nom du fichier choisi, sans soumettre.
    // Placeholder configurable via data-placeholder.
    Alpine.data('fileField', () => ({
        filename: '',
        placeholder: 'Choisir un fichier',
        init() { this.placeholder = this.$el.dataset.placeholder || this.placeholder; },
        get label() { return this.filename || this.placeholder; },
        pick(event) {
            const file = event.target.files && event.target.files[0];
            this.filename = file ? file.name : '';
        },
    }));

    // Fiche bien — zone d'upload multi-photos : affiche le nombre de fichiers
    // choisis et n'active le bouton d'envoi qu'une fois au moins un fichier retenu.
    Alpine.data('photosUpload', () => ({
        count: 0,
        get label() {
            if (this.count === 0) return 'Cliquez ou glissez vos photos ici';
            return this.count + (this.count > 1 ? ' photos sélectionnées' : ' photo sélectionnée');
        },
        get hasFiles() { return this.count > 0; },
        get submitClass() {
            return this.count > 0
                ? 'bg-teal text-paper hover:bg-teal-deep cursor-pointer'
                : 'bg-paper-dim text-muted cursor-not-allowed';
        },
        pick(event) {
            const files = event.target.files;
            this.count = files ? files.length : 0;
        },
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

    // Module Fiscalité — vue globale : filtrage client-side des échéances par type.
    // Le type de la chip cliquée est lu sur son data-filter (pas d'argument inline,
    // contrainte du build @alpinejs/csp). Masque les groupes vides + gère l'état vide.
    // Fiscalité — écran 1 : recherche client-side sur les cartes propriétaire.
    Alpine.data('fiscaliteProprietaires', () => ({
        q: '',
        filter() {
            const term = this.q.trim().toLowerCase();
            let anyVisible = false;
            this.$root.querySelectorAll('[data-owner]').forEach((card) => {
                const match = !term || (card.dataset.name || '').includes(term);
                card.style.display = match ? '' : 'none';
                if (match) anyVisible = true;
            });
            const empty = this.$root.querySelector('[data-empty-search]');
            if (empty) empty.classList.toggle('hidden', anyVisible);
        },
    }));

    // Fiscalité — écran 2 : carte de taxe repliable (registre de calcul).
    Alpine.data('taxCard', () => ({
        open: false,
        init() { this.open = this.$el.dataset.open === 'true'; },
        toggle() { this.open = !this.open; },
        get chevClass() { return this.open ? 'rotate-180' : ''; },
    }));
}

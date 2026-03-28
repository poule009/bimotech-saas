<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIMO-Tech — Logiciel de gestion immobilière pour agences au Sénégal</title>
    <meta name="description" content="BIMO-Tech simplifie la gestion de vos biens, contrats, locataires et paiements. Essai gratuit 30 jours.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800 font-sans antialiased">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- NAVBAR                                                            --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl font-black text-blue-900">BIMO</span>
                <span class="text-2xl font-black text-blue-500">Tech</span>
            </div>
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="#fonctionnalites" class="hover:text-blue-600 transition">Fonctionnalités</a>
                <a href="#tarifs" class="hover:text-blue-600 transition">Tarifs</a>
                <a href="#contact" class="hover:text-blue-600 transition">Contact</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}"
                   class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">
                    Connexion
                </a>
                <a href="{{ route('agency.register') }}"
                   class="text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    Essai gratuit
                </a>
            </div>
        </div>
    </nav>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- HERO                                                              --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <section class="pt-32 pb-20 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 text-white">
        <div class="max-w-6xl mx-auto px-4 text-center">

            <div class="inline-flex items-center gap-2 bg-blue-700/50 border border-blue-500/30 rounded-full px-4 py-1.5 text-sm text-blue-200 mb-6">
                🇸🇳 Conçu pour les agences immobilières au Sénégal
            </div>

            <h1 class="text-4xl md:text-6xl font-black leading-tight mb-6">
                Gérez votre agence<br>
                <span class="text-blue-300">immobilière simplement</span>
            </h1>

            <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto mb-10">
                BIMO-Tech centralise vos biens, contrats, locataires et paiements en un seul endroit.
                Générez vos quittances PDF, suivez vos commissions et relancez vos impayés en quelques clics.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('agency.register') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-blue-900 font-bold px-8 py-4 rounded-xl hover:bg-blue-50 transition text-lg shadow-lg">
                    🚀 Démarrer l'essai gratuit
                    <span class="text-sm font-normal text-blue-600">30 jours sans engagement</span>
                </a>
                <a href="#tarifs"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 border border-blue-400 text-white font-semibold px-8 py-4 rounded-xl hover:bg-blue-700 transition text-lg">
                    Voir les tarifs →
                </a>
            </div>

            {{-- Stats --}}
            <div class="mt-16 grid grid-cols-3 gap-8 max-w-lg mx-auto">
                <div>
                    <p class="text-3xl font-black text-white">30j</p>
                    <p class="text-sm text-blue-200 mt-1">Essai gratuit</p>
                </div>
                <div>
                    <p class="text-3xl font-black text-white">100%</p>
                    <p class="text-sm text-blue-200 mt-1">Données isolées</p>
                </div>
                <div>
                    <p class="text-3xl font-black text-white">FCFA</p>
                    <p class="text-sm text-blue-200 mt-1">Devise locale</p>
                </div>
            </div>

        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- FONCTIONNALITES                                                   --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <section id="fonctionnalites" class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4">

            <div class="text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-black text-gray-900">
                    Tout ce dont votre agence a besoin
                </h2>
                <p class="text-gray-500 mt-3 max-w-xl mx-auto">
                    Une plateforme complète pour gérer l'ensemble de votre activité immobilière.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                @php
                    $features = [
                        ['icon' => '🏠', 'titre' => 'Gestion des biens',       'desc' => 'Centralisez tous vos biens immobiliers avec photos, descriptions et statuts en temps réel.'],
                        ['icon' => '📋', 'titre' => 'Contrats de location',    'desc' => 'Créez et gérez vos contrats de bail. Suivez les dates d\'échéance et les renouvellements.'],
                        ['icon' => '💰', 'titre' => 'Suivi des paiements',     'desc' => 'Enregistrez les loyers encaissés, calculez vos commissions TTC et le net propriétaire automatiquement.'],
                        ['icon' => '📄', 'titre' => 'Quittances PDF',          'desc' => 'Générez des quittances de loyer professionnelles en PDF en un seul clic.'],
                        ['icon' => '⚠️', 'titre' => 'Gestion des impayés',    'desc' => 'Identifiez rapidement les locataires en retard et envoyez des relances automatiques.'],
                        ['icon' => '📊', 'titre' => 'Rapports financiers',     'desc' => 'Visualisez vos revenus, commissions et TVA collectée mois par mois.'],
                        ['icon' => '👥', 'titre' => 'Multi-utilisateurs',      'desc' => 'Donnez accès à vos propriétaires et locataires avec des espaces dédiés et sécurisés.'],
                        ['icon' => '🎨', 'titre' => 'Branding personnalisé',   'desc' => 'Ajoutez votre logo et votre couleur. Vos clients voient votre identité, pas la nôtre.'],
                        ['icon' => '🔒', 'titre' => 'Données 100% isolées',    'desc' => 'Chaque agence dispose de son espace privé. Aucune fuite de données entre agences.'],
                    ];
                @endphp

                @foreach ($features as $feature)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition">
                        <div class="text-3xl mb-4">{{ $feature['icon'] }}</div>
                        <h3 class="font-bold text-gray-900 text-lg mb-2">{{ $feature['titre'] }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach

            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TARIFS                                                            --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <section id="tarifs" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4">

            <div class="text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-black text-gray-900">
                    Des tarifs clairs et transparents
                </h2>
                <p class="text-gray-500 mt-3 max-w-xl mx-auto">
                    Commencez gratuitement pendant 30 jours. Aucune carte bancaire requise.
                </p>
            </div>

            {{-- Essai gratuit --}}
            <div class="bg-blue-50 border-2 border-blue-200 rounded-2xl p-8 text-center mb-10 max-w-2xl mx-auto">
                <p class="text-5xl font-black text-blue-900 mb-2">30 jours</p>
                <p class="text-xl font-semibold text-blue-700 mb-3">Essai gratuit — sans engagement</p>
                <p class="text-gray-600 mb-6">Accès complet à toutes les fonctionnalités. Aucune carte bancaire requise.</p>
                <a href="{{ route('agency.register') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-4 rounded-xl transition text-lg">
                    🚀 Démarrer gratuitement
                </a>
            </div>

            {{-- Plans payants --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                @php
                    $plans = [
                        ['label' => 'Mensuel',     'prix' => '25 000',  'duree' => '/mois',    'eco' => null,  'populaire' => false],
                        ['label' => 'Trimestriel', 'prix' => '67 500',  'duree' => '/3 mois',  'eco' => '10%', 'populaire' => false],
                        ['label' => 'Semestriel',  'prix' => '127 500', 'duree' => '/6 mois',  'eco' => '15%', 'populaire' => true],
                        ['label' => 'Annuel',      'prix' => '240 000', 'duree' => '/an',      'eco' => '20%', 'populaire' => false],
                    ];
                @endphp

                @foreach ($plans as $plan)
                    <div class="bg-white rounded-xl border-2 {{ $plan['populaire'] ? 'border-blue-400 shadow-lg' : 'border-gray-200 shadow-sm' }} p-6 flex flex-col relative">

                        @if ($plan['populaire'])
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                                <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    POPULAIRE
                                </span>
                            </div>
                        @endif

                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                            {{ $plan['label'] }}
                        </p>
                        <div class="mb-1">
                            <span class="text-3xl font-black text-gray-900">{{ $plan['prix'] }}</span>
                            <span class="text-base text-gray-500"> FCFA</span>
                        </div>
                        <p class="text-sm text-gray-400 mb-2">{{ $plan['duree'] }}</p>
                        @if ($plan['eco'])
                            <p class="text-sm text-green-600 font-semibold mb-4">
                                Économisez {{ $plan['eco'] }}
                            </p>
                        @else
                            <div class="mb-4"></div>
                        @endif

                        <a href="{{ route('agency.register') }}"
                           class="mt-auto w-full text-center py-2.5 px-4 rounded-lg text-sm font-semibold transition
                               {{ $plan['populaire']
                                   ? 'bg-blue-600 hover:bg-blue-700 text-white'
                                   : 'bg-gray-100 hover:bg-gray-200 text-gray-800' }}">
                            Commencer l'essai
                        </a>
                    </div>
                @endforeach

            </div>

            <p class="text-center text-gray-400 text-sm mt-6">
                Tous les plans incluent l'accès complet à toutes les fonctionnalités.
            </p>

        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- CTA FINAL                                                         --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <section class="py-20 bg-blue-900 text-white">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-black mb-4">
                Prêt à moderniser votre agence ?
            </h2>
            <p class="text-blue-200 text-lg mb-8">
                Rejoignez les agences immobilières qui font confiance à BIMO-Tech.
                Démarrez votre essai gratuit aujourd'hui — aucune carte requise.
            </p>
            <a href="{{ route('agency.register') }}"
               class="inline-flex items-center gap-2 bg-white text-blue-900 font-bold px-10 py-4 rounded-xl hover:bg-blue-50 transition text-lg shadow-lg">
                🚀 Créer mon espace agence gratuitement
            </a>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- CONTACT & FOOTER                                                  --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <section id="contact" class="py-16 bg-gray-900 text-white">
        <div class="max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-2xl font-black text-white">BIMO</span>
                        <span class="text-2xl font-black text-blue-400">Tech</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        La plateforme SaaS de gestion immobilière conçue pour les agences au Sénégal.
                    </p>
                </div>

                <div>
                    <h3 class="font-bold text-white mb-4">Contact</h3>
                    <div class="space-y-2 text-sm text-gray-400">
                        <p>📧 support@bimotech.sn</p>
                        <p>📞 +221 33 800 00 01</p>
                        <p>📍 Plateau, Dakar, Sénégal</p>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-white mb-4">Liens utiles</h3>
                    <div class="space-y-2 text-sm">
                        <p>
                            <a href="{{ route('agency.register') }}" class="text-gray-400 hover:text-white transition">
                                → Créer un compte
                            </a>
                        </p>
                        <p>
                            <a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition">
                                → Se connecter
                            </a>
                        </p>
                        <p>
                            <a href="#tarifs" class="text-gray-400 hover:text-white transition">
                                → Nos tarifs
                            </a>
                        </p>
                    </div>
                </div>

            </div>

            <div class="border-t border-gray-700 mt-10 pt-6 text-center text-xs text-gray-500">
                © {{ date('Y') }} BIMO-Tech. Tous droits réservés. Conçu au Sénégal 🇸🇳
            </div>

        </div>
    </section>

</body>
</html>


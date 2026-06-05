<x-guest-layout>

    <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-6 space-y-5">

        <div>
            <h2 class="font-display font-bold text-base text-bimo-text mb-1">Vérifiez votre email</h2>
            <p class="font-body text-sm text-bimo-text/50 leading-relaxed">
                Bienvenue ! Avant de commencer, cliquez sur le lien de vérification que nous venons de vous envoyer.
                Si vous n'avez pas reçu l'email, nous pouvons vous en envoyer un autre.
            </p>
        </div>

        @if(session('status') === 'verification-link-sent')
        <div class="flex items-center gap-3 bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[10px] px-4 py-3">
            <svg class="w-4 h-4 text-bimo-gold flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <p class="font-body text-sm text-bimo-gold">
                Un nouveau lien de vérification a été envoyé à votre adresse email.
            </p>
        </div>
        @endif

        <div class="flex items-center justify-between gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[10px]
                               bg-bimo-navy hover:bg-bimo-navy-dk text-white
                               font-display font-bold text-sm transition-colors duration-150">
                    Renvoyer l'email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="font-body text-sm text-bimo-text/50 hover:text-bimo-text transition-colors duration-150">
                    Se déconnecter
                </button>
            </form>
        </div>

    </div>

</x-guest-layout>

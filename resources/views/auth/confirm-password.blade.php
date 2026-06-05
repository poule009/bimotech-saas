<x-guest-layout>

    <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-6 space-y-5">

        <div>
            <h2 class="font-display font-bold text-base text-bimo-text mb-1">Zone sécurisée</h2>
            <p class="font-body text-sm text-bimo-text/50">
                Veuillez confirmer votre mot de passe avant de continuer.
            </p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="password" class="block font-body font-medium text-sm text-bimo-text">
                    Mot de passe <span class="text-bimo-red">*</span>
                </label>
                <input id="password" type="password" name="password"
                       required autocomplete="current-password"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                              placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                              @error('password') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                              @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                @error('password')
                <p class="font-body text-xs text-bimo-red">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[10px]
                               bg-bimo-navy hover:bg-bimo-navy-dk text-white
                               font-display font-bold text-sm transition-colors duration-150">
                    Confirmer
                </button>
            </div>
        </form>

    </div>

</x-guest-layout>

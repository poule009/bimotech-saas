@php
    // Bouton flottant « support WhatsApp » : permet à une agence de joindre
    // BimoTech à tout moment. Numéro centralisé dans config/services.php.
    $numero = preg_replace('/\D/', '', (string) config('services.bimotech.whatsapp'));

    $agence  = auth()->user()?->agency?->name;
    $message = 'Bonjour, j’ai besoin d’aide avec Bimmo'
             . ($agence ? ' (agence ' . $agence . ')' : '') . '.';

    $lien = $numero !== ''
        ? 'https://wa.me/' . $numero . '?text=' . rawurlencode($message)
        : null;
@endphp

@if($lien)
    <a href="{{ $lien }}" target="_blank" rel="noopener"
       aria-label="Contacter le support sur WhatsApp"
       class="group fixed bottom-5 right-5 z-50 flex items-center gap-2.5 rounded-full
              bg-[#25D366] text-white pl-4 pr-5 py-3 shadow-lg shadow-black/15
              hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
              focus-visible:ring-[#25D366] transition-opacity">
        <x-icon-whatsapp :size="22" />
        <span class="text-[13.5px] font-bold whitespace-nowrap hidden sm:inline">Besoin d’aide ?</span>
    </a>
@endif

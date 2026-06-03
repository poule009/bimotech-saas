{{--
    Partial : @include('partials.public-nav', ['active' => 'contact'])
    $active : '', 'fonctionnalites', 'conformite', 'tarifs', 'faq', 'contact'
--}}

@php $active = $active ?? ''; @endphp

<nav class="fixed top-0 left-0 right-0 z-[200] px-[5%] h-16 flex items-center justify-between bg-[rgba(255,255,255,.97)] backdrop-blur-md border-b border-[rgba(0,0,0,.07)] shadow-[0_1px_8px_rgba(0,0,0,.06)]" role="navigation" aria-label="Navigation principale">
    <a href="{{ url('/') }}" class="no-underline flex items-center flex-shrink-0" aria-label="BiMO-tech Immo — Accueil">
        <img src="/images/logo.jpeg" alt="BiMO-tech Immo" class="h-9 w-auto">
    </a>

    {{-- Desktop links --}}
    <div class="hidden md:flex items-center gap-7" role="menubar">
        @foreach([
            ['fonctionnalites', url('/') . '#fonctionnalites', 'Fonctionnalités'],
            ['conformite',      url('/') . '#conformite',      'Conformité'],
            ['pricing',         route('pricing'),              'Tarifs'],
            ['faq',             route('faq'),                  'FAQ'],
            ['contact',         route('contact'),              'Contact'],
        ] as [$key, $href, $label])
        <a href="{{ $href }}" role="menuitem"
           class="font-body text-[13.5px] no-underline transition-colors duration-200 whitespace-nowrap {{ $active === $key ? 'text-bimo-gold font-semibold' : 'text-[#6b7280] hover:text-[#0d1117]' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="flex items-center gap-2 flex-shrink-0">
        <a href="{{ route('login') }}" class="hidden md:block font-body text-[13.5px] text-[#374151] no-underline px-3.5 py-2 rounded-[8px] hover:text-[#0d1117] transition-colors duration-200 whitespace-nowrap">Connexion</a>
        <a href="{{ route('agency.register') }}" class="font-body font-semibold text-[13px] text-white no-underline px-4 py-2 rounded-[8px] bg-bimo-gold hover:opacity-90 transition-opacity duration-200 whitespace-nowrap hidden xs:block">Démarrer gratuitement</a>
        <button id="hamburger" class="md:hidden flex flex-col gap-[5px] bg-transparent border-none cursor-pointer p-2 rounded-[8px] hover:bg-[rgba(0,0,0,.05)] transition-colors" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="mobile-drawer">
            <span id="ham-1" class="block w-5 h-[2px] bg-[#6b7280] rounded-[2px] transition-transform duration-250"></span>
            <span id="ham-2" class="block w-5 h-[2px] bg-[#6b7280] rounded-[2px] transition-opacity duration-250"></span>
            <span id="ham-3" class="block w-5 h-[2px] bg-[#6b7280] rounded-[2px] transition-transform duration-250"></span>
        </button>
    </div>
</nav>

{{-- Drawer mobile --}}
<div id="mobile-drawer" class="fixed top-16 left-0 right-0 bottom-0 bg-white z-[190] translate-x-full transition-transform duration-300 ease-[cubic-bezier(.4,0,.2,1)] flex flex-col px-[5%] py-6 border-t border-[rgba(0,0,0,.07)] overflow-y-auto" aria-hidden="true" role="dialog" aria-label="Menu mobile">
    @foreach([
        ['fonctionnalites', url('/') . '#fonctionnalites', 'Fonctionnalités'],
        ['conformite',      url('/') . '#conformite',      'Conformité'],
        ['pricing',         route('pricing'),              'Tarifs'],
        ['faq',             route('faq'),                  'FAQ'],
        ['contact',         route('contact'),              'Contact'],
    ] as [$key, $href, $label])
    <a href="{{ $href }}" onclick="closeDrawer()"
       class="block py-3.5 font-body text-base no-underline border-b border-[rgba(0,0,0,.06)] transition-colors duration-200 {{ $active === $key ? 'text-bimo-gold' : 'text-[#6b7280] hover:text-bimo-gold' }}">
        {{ $label }}
    </a>
    @endforeach
    <div class="mt-6 flex flex-col gap-2.5">
        <a href="{{ route('agency.register') }}" class="block text-center font-body font-bold text-[15px] text-white no-underline py-3.5 px-4 rounded-[10px] bg-bimo-gold hover:opacity-90 transition-opacity duration-200">Créer mon agence gratuitement →</a>
        <a href="{{ route('login') }}" class="block text-center font-body text-[15px] text-[#374151] no-underline py-3.5 px-4 rounded-[10px] border border-[rgba(0,0,0,.1)] hover:text-[#0d1117] hover:border-[rgba(0,0,0,.2)] transition-all duration-200">Se connecter</a>
    </div>
</div>

<script>
(function(){
    var btn    = document.getElementById('hamburger');
    var drawer = document.getElementById('mobile-drawer');
    var h1 = document.getElementById('ham-1'), h2 = document.getElementById('ham-2'), h3 = document.getElementById('ham-3');
    function openDrawer(){
        drawer.classList.remove('translate-x-full');
        btn.setAttribute('aria-expanded','true');
        drawer.setAttribute('aria-hidden','false');
        document.body.style.overflow='hidden';
        h1.style.transform='rotate(45deg) translate(5px,5px)';
        h2.style.opacity='0';
        h3.style.transform='rotate(-45deg) translate(5px,-5px)';
    }
    window.closeDrawer = function(){
        drawer.classList.add('translate-x-full');
        btn.setAttribute('aria-expanded','false');
        drawer.setAttribute('aria-hidden','true');
        document.body.style.overflow='';
        h1.style.transform=''; h2.style.opacity=''; h3.style.transform='';
    };
    btn.addEventListener('click', function(){ drawer.classList.contains('translate-x-full') ? openDrawer() : closeDrawer(); });
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeDrawer(); });
})();
</script>

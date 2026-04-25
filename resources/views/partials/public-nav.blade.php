{{--
    Partial : @include('partials.public-nav')
    À inclure dans toutes les pages publiques.
    Props via @include(['active' => 'contact']) pour surligner le lien actif.
--}}

@props(['active' => ''])

<style>
.pub-nav{
    position:fixed;top:0;left:0;right:0;z-index:200;
    padding:0 5%;height:64px;
    display:flex;align-items:center;justify-content:space-between;
    background:rgba(250,249,246,.97);
    backdrop-filter:blur(16px);
    -webkit-backdrop-filter:blur(16px);
    border-bottom:1px solid rgba(0,0,0,.07);
    transition:background .3s;
    box-shadow:0 1px 8px rgba(0,0,0,.06);
}
.pub-nav-logo{font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:#c9a84c;text-decoration:none;letter-spacing:-.5px;flex-shrink:0}
.pub-nav-logo span{color:#0d1117}

.pub-nav-links{display:flex;align-items:center;gap:1.75rem}
.pub-nav-links a{color:#6b7280;text-decoration:none;font-size:13.5px;transition:color .2s;white-space:nowrap}
.pub-nav-links a:hover,.pub-nav-links a.active-link{color:#0d1117}
.pub-nav-links a.active-link{color:#c9a84c}

.pub-nav-actions{display:flex;align-items:center;gap:8px;flex-shrink:0}
.pub-nav-login{color:#374151;text-decoration:none;font-size:13.5px;padding:8px 14px;border-radius:8px;transition:color .2s;white-space:nowrap}
.pub-nav-login:hover{color:#0d1117}
.pub-nav-cta{background:#0d1117;color:#c9a84c;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;padding:8px 18px;border-radius:8px;text-decoration:none;transition:all .2s;white-space:nowrap;border:1px solid rgba(201,168,76,.2)}
.pub-nav-cta:hover{background:#c9a84c;color:#0d1117;border-color:#c9a84c}

/* Hamburger */
.hamburger{
    display:none;
    flex-direction:column;gap:5px;
    background:none;border:none;cursor:pointer;padding:8px;
    border-radius:8px;transition:background .2s;
}
.hamburger:hover{background:rgba(0,0,0,.05)}
.hamburger span{display:block;width:20px;height:2px;background:#6b7280;border-radius:2px;transition:all .25s}
.hamburger.open span:nth-child(1){transform:rotate(45deg) translate(5px,5px)}
.hamburger.open span:nth-child(2){opacity:0;transform:scaleX(0)}
.hamburger.open span:nth-child(3){transform:rotate(-45deg) translate(5px,-5px)}

/* Drawer mobile */
.mobile-drawer{
    position:fixed;top:64px;left:0;right:0;bottom:0;
    background:#fff;
    z-index:190;
    transform:translateX(100%);
    transition:transform .3s cubic-bezier(.4,0,.2,1);
    display:flex;flex-direction:column;
    padding:1.5rem 5%;
    border-top:1px solid rgba(0,0,0,.07);
    overflow-y:auto;
}
.mobile-drawer.open{transform:translateX(0)}
.mobile-drawer a{
    display:block;padding:14px 0;
    font-size:16px;color:#6b7280;text-decoration:none;
    border-bottom:1px solid rgba(0,0,0,.06);
    transition:color .2s;
}
.mobile-drawer a:hover,.mobile-drawer a.active-link{color:#c9a84c}
.mobile-drawer-actions{margin-top:1.5rem;display:flex;flex-direction:column;gap:10px}
.mobile-drawer .mob-cta{background:#c9a84c;color:#0d1117;font-weight:700;font-size:15px;padding:14px;border-radius:10px;text-align:center;text-decoration:none;transition:opacity .2s}
.mobile-drawer .mob-cta:hover{opacity:.9}
.mobile-drawer .mob-login{border:1px solid rgba(0,0,0,.1);color:#374151;font-size:15px;padding:14px;border-radius:10px;text-align:center;text-decoration:none;transition:all .2s}
.mobile-drawer .mob-login:hover{color:#0d1117;border-color:rgba(0,0,0,.2)}

@media(max-width:860px){
    .pub-nav-links{display:none}
    .pub-nav-login{display:none}
    .hamburger{display:flex}
}
@media(max-width:480px){
    .pub-nav-cta{display:none}
}
</style>

<nav class="pub-nav" role="navigation" aria-label="Navigation principale">
    <a href="{{ url('/') }}" class="pub-nav-logo" aria-label="BimoTech Immo — Accueil">Bimo<span>Tech</span></a>

    <div class="pub-nav-links" role="menubar">
        <a href="#fonctionnalites" role="menuitem" @if($active==='fonctionnalites') class="active-link" aria-current="page" @endif>Fonctionnalités</a>
        <a href="#conformite"      role="menuitem" @if($active==='conformite')      class="active-link" aria-current="page" @endif>Conformité</a>
        <a href="#tarifs"          role="menuitem" @if($active==='tarifs')          class="active-link" aria-current="page" @endif>Tarifs</a>
        <a href="{{ route('faq') }}"     role="menuitem" @if($active==='faq')     class="active-link" aria-current="page" @endif>FAQ</a>
        <a href="{{ route('contact') }}" role="menuitem" @if($active==='contact') class="active-link" aria-current="page" @endif>Contact</a>
    </div>

    <div class="pub-nav-actions">
        <a href="{{ route('login') }}"    class="pub-nav-login" aria-label="Se connecter">Connexion</a>
        <a href="{{ route('agency.register') }}" class="pub-nav-cta"   aria-label="Créer un compte gratuitement">Démarrer gratuitement</a>
        <button class="hamburger" id="hamburger" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="mobile-drawer">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<div class="mobile-drawer" id="mobile-drawer" aria-hidden="true" role="dialog" aria-label="Menu mobile">
    <a href="#fonctionnalites" onclick="closeDrawer()" @if($active==='fonctionnalites') class="active-link" @endif>Fonctionnalités</a>
    <a href="#conformite"      onclick="closeDrawer()" @if($active==='conformite')      class="active-link" @endif>Conformité</a>
    <a href="#tarifs"          onclick="closeDrawer()" @if($active==='tarifs')          class="active-link" @endif>Tarifs</a>
    <a href="{{ route('faq') }}"     @if($active==='faq')     class="active-link" @endif>FAQ</a>
    <a href="{{ route('contact') }}" @if($active==='contact') class="active-link" @endif>Contact</a>
    <div class="mobile-drawer-actions">
        <a href="{{ route('agency.register') }}" class="mob-cta">Créer mon agence gratuitement →</a>
        <a href="{{ route('login') }}"    class="mob-login">Se connecter</a>
    </div>
</div>

<script>
(function(){
    const btn    = document.getElementById('hamburger');
    const drawer = document.getElementById('mobile-drawer');
    function openDrawer(){
        btn.classList.add('open');
        drawer.classList.add('open');
        btn.setAttribute('aria-expanded','true');
        drawer.setAttribute('aria-hidden','false');
        document.body.style.overflow='hidden';
    }
    window.closeDrawer = function(){
        btn.classList.remove('open');
        drawer.classList.remove('open');
        btn.setAttribute('aria-expanded','false');
        drawer.setAttribute('aria-hidden','true');
        document.body.style.overflow='';
    }
    btn.addEventListener('click',function(){
        btn.classList.contains('open') ? closeDrawer() : openDrawer();
    });
    document.addEventListener('keydown',function(e){
        if(e.key==='Escape') closeDrawer();
    });
})();
</script>
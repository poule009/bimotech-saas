

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['active' => '']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['active' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<style>
.pub-nav{
    position:fixed;top:0;left:0;right:0;z-index:200;
    padding:0 5%;height:64px;
    display:flex;align-items:center;justify-content:space-between;
    background:rgba(13,17,23,.9);
    backdrop-filter:blur(16px);
    -webkit-backdrop-filter:blur(16px);
    border-bottom:1px solid rgba(255,255,255,.07);
    transition:background .3s;
}
.pub-nav-logo{font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:#c9a84c;text-decoration:none;letter-spacing:-.5px;flex-shrink:0}
.pub-nav-logo span{color:#e6edf3}

.pub-nav-links{display:flex;align-items:center;gap:1.75rem}
.pub-nav-links a{color:#8b949e;text-decoration:none;font-size:13.5px;transition:color .2s;white-space:nowrap}
.pub-nav-links a:hover,.pub-nav-links a.active-link{color:#e6edf3}
.pub-nav-links a.active-link{color:#c9a84c}

.pub-nav-actions{display:flex;align-items:center;gap:8px;flex-shrink:0}
.pub-nav-login{color:#8b949e;text-decoration:none;font-size:13.5px;padding:8px 14px;border-radius:8px;transition:color .2s;white-space:nowrap}
.pub-nav-login:hover{color:#e6edf3}
.pub-nav-cta{background:#c9a84c;color:#0d1117;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;padding:8px 18px;border-radius:8px;text-decoration:none;transition:opacity .2s;white-space:nowrap}
.pub-nav-cta:hover{opacity:.85}

/* Hamburger */
.hamburger{
    display:none;
    flex-direction:column;gap:5px;
    background:none;border:none;cursor:pointer;padding:8px;
    border-radius:8px;transition:background .2s;
}
.hamburger:hover{background:rgba(255,255,255,.06)}
.hamburger span{display:block;width:20px;height:2px;background:#8b949e;border-radius:2px;transition:all .25s}
.hamburger.open span:nth-child(1){transform:rotate(45deg) translate(5px,5px)}
.hamburger.open span:nth-child(2){opacity:0;transform:scaleX(0)}
.hamburger.open span:nth-child(3){transform:rotate(-45deg) translate(5px,-5px)}

/* Drawer mobile */
.mobile-drawer{
    position:fixed;top:64px;left:0;right:0;bottom:0;
    background:#0d1117;
    z-index:190;
    transform:translateX(100%);
    transition:transform .3s cubic-bezier(.4,0,.2,1);
    display:flex;flex-direction:column;
    padding:1.5rem 5%;
    border-top:1px solid rgba(255,255,255,.06);
    overflow-y:auto;
}
.mobile-drawer.open{transform:translateX(0)}
.mobile-drawer a{
    display:block;padding:14px 0;
    font-size:16px;color:#8b949e;text-decoration:none;
    border-bottom:1px solid rgba(255,255,255,.05);
    transition:color .2s;
}
.mobile-drawer a:hover,.mobile-drawer a.active-link{color:#c9a84c}
.mobile-drawer-actions{margin-top:1.5rem;display:flex;flex-direction:column;gap:10px}
.mobile-drawer .mob-cta{background:#c9a84c;color:#0d1117;font-weight:700;font-size:15px;padding:14px;border-radius:10px;text-align:center;text-decoration:none;transition:opacity .2s}
.mobile-drawer .mob-cta:hover{opacity:.9}
.mobile-drawer .mob-login{border:1px solid rgba(255,255,255,.1);color:#8b949e;font-size:15px;padding:14px;border-radius:10px;text-align:center;text-decoration:none;transition:all .2s}
.mobile-drawer .mob-login:hover{color:#e6edf3;border-color:rgba(255,255,255,.2)}

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
    <a href="<?php echo e(url('/')); ?>" class="pub-nav-logo" aria-label="BimoTech Immo — Accueil">Bimo<span>Tech</span></a>

    <div class="pub-nav-links" role="menubar">
        <a href="#fonctionnalites" role="menuitem" <?php if($active==='fonctionnalites'): ?> class="active-link" aria-current="page" <?php endif; ?>>Fonctionnalités</a>
        <a href="#conformite"      role="menuitem" <?php if($active==='conformite'): ?>      class="active-link" aria-current="page" <?php endif; ?>>Conformité</a>
        <a href="#tarifs"          role="menuitem" <?php if($active==='tarifs'): ?>          class="active-link" aria-current="page" <?php endif; ?>>Tarifs</a>
        <a href="<?php echo e(route('faq')); ?>"     role="menuitem" <?php if($active==='faq'): ?>     class="active-link" aria-current="page" <?php endif; ?>>FAQ</a>
        <a href="<?php echo e(route('contact')); ?>" role="menuitem" <?php if($active==='contact'): ?> class="active-link" aria-current="page" <?php endif; ?>>Contact</a>
    </div>

    <div class="pub-nav-actions">
        <a href="<?php echo e(route('login')); ?>"    class="pub-nav-login" aria-label="Se connecter">Connexion</a>
        <a href="<?php echo e(route('agency.register')); ?>" class="pub-nav-cta"   aria-label="Créer un compte gratuitement">Démarrer gratuitement</a>
        <button class="hamburger" id="hamburger" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="mobile-drawer">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<div class="mobile-drawer" id="mobile-drawer" aria-hidden="true" role="dialog" aria-label="Menu mobile">
    <a href="#fonctionnalites" onclick="closeDrawer()" <?php if($active==='fonctionnalites'): ?> class="active-link" <?php endif; ?>>Fonctionnalités</a>
    <a href="#conformite"      onclick="closeDrawer()" <?php if($active==='conformite'): ?>      class="active-link" <?php endif; ?>>Conformité</a>
    <a href="#tarifs"          onclick="closeDrawer()" <?php if($active==='tarifs'): ?>          class="active-link" <?php endif; ?>>Tarifs</a>
    <a href="<?php echo e(route('faq')); ?>"     <?php if($active==='faq'): ?>     class="active-link" <?php endif; ?>>FAQ</a>
    <a href="<?php echo e(route('contact')); ?>" <?php if($active==='contact'): ?> class="active-link" <?php endif; ?>>Contact</a>
    <div class="mobile-drawer-actions">
        <a href="<?php echo e(route('agency.register')); ?>" class="mob-cta">Créer mon agence gratuitement →</a>
        <a href="<?php echo e(route('login')); ?>"    class="mob-login">Se connecter</a>
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
</script><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/partials/public-nav.blade.php ENDPATH**/ ?>
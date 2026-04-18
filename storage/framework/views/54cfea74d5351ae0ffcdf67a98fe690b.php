<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Paiement annulé — BimoTech</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{
    font-family:'DM Sans',sans-serif;
    background:#080c12;color:#e6edf3;
    min-height:100vh;
    display:flex;align-items:center;justify-content:center;
    padding:24px;
    position:relative;overflow:hidden;
}
body::before{
    content:'';position:absolute;top:50%;left:50%;
    transform:translate(-50%,-60%);
    width:700px;height:400px;
    background:radial-gradient(ellipse,rgba(239,68,68,.05) 0%,transparent 70%);
    pointer-events:none;
}
.card{
    background:#0d1117;
    border:1px solid rgba(255,255,255,.08);
    border-radius:20px;
    padding:48px 40px;
    max-width:460px;width:100%;
    text-align:center;
    box-shadow:0 40px 80px rgba(0,0,0,.5);
    position:relative;z-index:1;
}
.error-ring{
    width:80px;height:80px;border-radius:50%;
    background:rgba(239,68,68,.08);
    border:1.5px solid rgba(239,68,68,.2);
    display:flex;align-items:center;justify-content:center;
    margin:0 auto 28px;
    box-shadow:0 0 40px rgba(239,68,68,.08);
}
h1{font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:#e6edf3;margin-bottom:10px;}
.sub-title{font-size:14px;color:#8b949e;line-height:1.65;margin-bottom:28px;}

.tips-card{
    background:#161b22;
    border:1px solid rgba(234,179,8,.15);
    border-radius:12px;padding:18px 20px;
    margin-bottom:28px;text-align:left;
}
.tips-title{
    font-family:'Syne',sans-serif;font-size:12px;font-weight:700;
    color:#fbbf24;text-transform:uppercase;letter-spacing:.8px;margin-bottom:12px;
}
.tip{
    display:flex;align-items:flex-start;gap:10px;
    font-size:13px;color:#8b949e;line-height:1.55;
    margin-bottom:8px;
}
.tip:last-child{margin-bottom:0}
.tip-dot{
    width:5px;height:5px;background:#fbbf24;border-radius:50%;
    flex-shrink:0;margin-top:7px;opacity:.6;
}

.btn-primary{
    display:flex;align-items:center;justify-content:center;gap:8px;
    background:linear-gradient(135deg,#c9a84c,#e8c96a);
    color:#080c12;font-family:'DM Sans',sans-serif;
    font-weight:700;font-size:14px;
    padding:13px 24px;border-radius:10px;
    text-decoration:none;transition:all .2s;
    box-shadow:0 4px 16px rgba(201,168,76,.2);
    margin-bottom:10px;
}
.btn-primary:hover{opacity:.9;transform:translateY(-1px)}
.btn-secondary{
    display:flex;align-items:center;justify-content:center;gap:8px;
    background:transparent;color:#8b949e;
    font-family:'DM Sans',sans-serif;font-weight:500;font-size:14px;
    padding:13px 24px;border-radius:10px;
    text-decoration:none;transition:all .2s;
    border:1px solid rgba(255,255,255,.1);
}
.btn-secondary:hover{border-color:rgba(255,255,255,.2);color:#e6edf3}

.support-row{
    margin-top:24px;padding-top:20px;
    border-top:1px solid rgba(255,255,255,.06);
    font-size:12px;color:#484f58;
    display:flex;flex-direction:column;gap:4px;
}
.support-contact{color:#8b949e;font-size:12.5px}
.logo{font-family:'Syne',sans-serif;font-size:14px;font-weight:800;color:#c9a84c;margin-bottom:28px;}
.logo span{color:#8b949e}
</style>
</head>
<body>
<div class="card">
    <div class="logo">Bimo<span>Tech</span> Immo</div>

    <div class="error-ring">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
        </svg>
    </div>

    <h1>Paiement annulé</h1>
    <p class="sub-title">
        Votre paiement n'a pas pu être traité ou a été annulé.<br>
        Aucun montant n'a été débité.
    </p>

    <div class="tips-card">
        <div class="tips-title">Que faire ?</div>
        <div class="tip"><div class="tip-dot"></div><span>Vérifiez que votre compte Wave ou Orange Money est suffisamment approvisionné.</span></div>
        <div class="tip"><div class="tip-dot"></div><span>Réessayez avec un autre mode de paiement disponible.</span></div>
        <div class="tip"><div class="tip-dot"></div><span>Si le problème persiste, contactez notre support — on vous aide rapidement.</span></div>
    </div>

    <a href="<?php echo e(route('subscription.index')); ?>" class="btn-primary">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
        Réessayer le paiement
    </a>
    <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn-secondary">
        Retour au tableau de bord
    </a>

    <div class="support-row">
        <span>Besoin d'aide ?</span>
        <span class="support-contact">📧 support@bimotech.sn &nbsp;·&nbsp; 📞 +221 33 800 00 01</span>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\Users\ph\bimotech-immo\resources\views/subscription/echec.blade.php ENDPATH**/ ?>
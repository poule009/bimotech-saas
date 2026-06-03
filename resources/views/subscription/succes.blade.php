<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Paiement réussi — BimoTech</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap"></noscript>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{
    font-family:'DM Sans',sans-serif;
    background:#080c12;
    color:#e6edf3;
    min-height:100vh;
    display:flex;align-items:center;justify-content:center;
    padding:24px;
    position:relative;overflow:hidden;
}
body::before{
    content:'';position:absolute;top:50%;left:50%;
    transform:translate(-50%,-60%);
    width:700px;height:400px;
    background:radial-gradient(ellipse,rgba(34,197,94,.06) 0%,transparent 70%);
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
.success-ring{
    width:80px;height:80px;border-radius:50%;
    background:rgba(34,197,94,.1);
    border:1.5px solid rgba(34,197,94,.25);
    display:flex;align-items:center;justify-content:center;
    margin:0 auto 28px;
    box-shadow:0 0 40px rgba(34,197,94,.12);
}
h1{
    font-family:'Syne',sans-serif;
    font-size:26px;font-weight:800;
    color:#e6edf3;margin-bottom:10px;
}
.sub-title{
    font-size:14px;color:#8b949e;line-height:1.65;
    margin-bottom:28px;
}
.sub-title strong{color:#c9d1d9}

.details-card{
    background:#161b22;
    border:1px solid rgba(34,197,94,.2);
    border-radius:12px;
    padding:18px 20px;
    margin-bottom:28px;
    text-align:left;
}
.details-row{
    display:flex;justify-content:space-between;align-items:center;
    padding:7px 0;
    border-bottom:1px solid rgba(255,255,255,.04);
    font-size:13px;
}
.details-row:last-child{border-bottom:none}
.details-label{color:#8b949e}
.details-value{color:#e6edf3;font-weight:600}
.details-value.green{color:#4ade80}

.btn-primary{
    display:flex;align-items:center;justify-content:center;gap:8px;
    background:linear-gradient(135deg,#c9a84c,#e8c96a);
    color:#080c12;font-family:'DM Sans',sans-serif;
    font-weight:700;font-size:14px;
    padding:13px 24px;border-radius:10px;
    text-decoration:none;transition:all .2s;
    box-shadow:0 4px 16px rgba(201,168,76,.25);
}
.btn-primary:hover{opacity:.9;transform:translateY(-1px)}

.footer-note{
    margin-top:24px;font-size:11.5px;color:#484f58;line-height:1.6;
}
.logo{
    font-family:'Syne',sans-serif;font-size:14px;font-weight:800;
    color:#c9a84c;margin-bottom:28px;
}
.logo span{color:#8b949e}
</style>
</head>
<body>
<div class="card">
    <div class="logo">Bimo<span>Tech</span> Immo</div>

    <div class="success-ring">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </div>

    <h1>Paiement réussi !</h1>
    <p class="sub-title">
        Votre abonnement BimoTech est maintenant actif pour
        <strong>{{ $agency->name }}</strong>. Bienvenue !
    </p>

    @if($agency->subscription && $agency->subscription->estActif())
    <div class="details-card">
        <div class="details-row">
            <span class="details-label">Statut</span>
            <span class="details-value green">Actif</span>
        </div>
        <div class="details-row">
            <span class="details-label">Plan</span>
            <span class="details-value">{{ \App\Models\Subscription::LABELS[$agency->subscription->plan] ?? $agency->subscription->plan }}</span>
        </div>
        <div class="details-row">
            <span class="details-label">Expire le</span>
            <span class="details-value">{{ $agency->subscription->date_fin_abonnement->format('d/m/Y') }}</span>
        </div>
        <div class="details-row">
            <span class="details-label">Jours restants</span>
            <span class="details-value green">{{ $agency->subscription->joursRestantsAbonnement() }} jours</span>
        </div>
    </div>
    @endif

    <a href="{{ route('admin.dashboard') }}" class="btn-primary">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        Accéder au tableau de bord
    </a>

    <p class="footer-note">
        Un email de confirmation a été envoyé à {{ $agency->email ?? auth()->user()->email }}.<br>
        Questions ? <strong style="color:#8b949e">support@bimotech.sn</strong>
    </p>
</div>
</body>
</html>

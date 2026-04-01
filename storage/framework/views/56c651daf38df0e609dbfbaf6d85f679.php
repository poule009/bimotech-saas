<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Bimotech')); ?></title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; color: inherit; }

        .auth-wrapper {
            width: 100%;
            max-width: 420px;
        }

        /* Logo / Brand */
        .auth-brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .auth-brand-logo {
            width: 48px;
            height: 48px;
            background: #1a3c5e;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            box-shadow: 0 4px 14px rgba(26,60,94,.25);
        }
        .auth-brand-logo svg { width: 24px; height: 24px; color: white; }
        .auth-brand-name {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -.5px;
        }
        .auth-brand-sub {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
        }

        /* Carte */
        .auth-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.07), 0 2px 4px -2px rgba(0,0,0,.05);
        }
        .auth-card-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
            letter-spacing: -.3px;
        }
        .auth-card-sub {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 24px;
        }

        /* Form */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #0f172a;
            background: white;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
            font-family: inherit;
        }
        .form-input:focus {
            border-color: #1a3c5e;
            box-shadow: 0 0 0 3px rgba(26,60,94,.12);
        }
        .form-input::placeholder { color: #94a3b8; }
        .form-error {
            font-size: 12px;
            color: #dc2626;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Status */
        .form-status {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
            font-size: 13px;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 16px;
        }

        /* Checkbox */
        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .form-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 4px;
            accent-color: #1a3c5e;
            cursor: pointer;
        }
        .form-check label {
            font-size: 13px;
            color: #64748b;
            cursor: pointer;
        }

        /* Bouton principal */
        .btn-auth {
            width: 100%;
            padding: 11px 20px;
            background: #1a3c5e;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: filter .15s, transform .1s;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-auth:hover { filter: brightness(.9); }
        .btn-auth:active { transform: scale(.99); }

        /* Lien secondaire */
        .auth-link {
            font-size: 13px;
            color: #1a3c5e;
            font-weight: 500;
            transition: opacity .15s;
        }
        .auth-link:hover { opacity: .75; text-decoration: underline; }

        /* Footer */
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #94a3b8;
        }
        .auth-footer a { color: #64748b; font-weight: 500; }
        .auth-footer a:hover { color: #1a3c5e; }

        /* Divider */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
            color: #94a3b8;
            font-size: 12px;
        }
        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">

        
        <div class="auth-brand">
            <div class="auth-brand-logo">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 00-1-1h-2a1 1 0 00-1 1v5m4 0H9"/>
                </svg>
            </div>
            <div class="auth-brand-name">Bimotech</div>
            <div class="auth-brand-sub">Gestion immobilière professionnelle</div>
        </div>

        
        <?php echo e($slot); ?>


        
        <div class="auth-footer">
            © <?php echo e(date('Y')); ?> Bimotech — Conçu au Sénégal 🇸🇳
        </div>

    </div>
</body>
</html><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/layouts/guest.blade.php ENDPATH**/ ?>
<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function showSetup(Request $request): View
    {
        $user = $request->user();

        if (! $request->session()->has('2fa_setup_secret')) {
            $secret = $this->google2fa->generateSecretKey();
            $request->session()->put('2fa_setup_secret', $secret);
        }

        $secret = $request->session()->get('2fa_setup_secret');

        $otpUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $qrCode = $this->generateQrCodeSvg($otpUrl);

        return view('superadmin.2fa.setup', compact('qrCode', 'secret'));
    }

    public function confirmSetup(Request $request): RedirectResponse|View
    {
        $request->validate(['code' => 'required|digits:6']);

        $key = 'two_factor_setup.' . $request->user()->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['code' => "Trop de tentatives, réessayez dans {$seconds} secondes."]);
        }

        $secret = $request->session()->get('2fa_setup_secret');

        if (! $secret) {
            return redirect()->route('superadmin.2fa.setup')
                ->withErrors(['code' => 'Session expirée, recommencez le setup.']);
        }

        $valid = $this->google2fa->verifyKey($secret, $request->code, 1);

        if (! $valid) {
            RateLimiter::hit($key, 60);
            return back()->withErrors(['code' => 'Code incorrect, réessayez.']);
        }

        RateLimiter::clear($key);

        $user = $request->user();
        $user->two_factor_secret = $secret;
        $user->two_factor_confirmed_at = now();
        $user->save();

        $request->session()->forget('2fa_setup_secret');
        $request->session()->put('two_factor_verified', true);

        $recoveryCodes = $user->generateRecoveryCodes();

        return view('superadmin.2fa.recovery-codes', ['codes' => $recoveryCodes]);
    }

    public function showChallenge(Request $request): RedirectResponse|View
    {
        if ($request->session()->get('two_factor_verified') === true) {
            return redirect()->route('superadmin.dashboard');
        }

        return view('superadmin.2fa.challenge');
    }

    public function verifyChallenge(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        $key = 'two_factor_challenge.' . $request->user()->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['code' => "Trop de tentatives, réessayez dans {$seconds} secondes."])
                ->withInput();
        }

        $user = $request->user();
        $verified = false;

        $codeInput = trim($request->code);

        if (ctype_digit($codeInput) && strlen($codeInput) === 6) {
            $verified = (bool) $this->google2fa->verifyKey(
                $user->two_factor_secret,
                $codeInput,
                1
            );
        }

        if (! $verified) {
            $verified = $user->useRecoveryCode($codeInput);
        }

        if (! $verified) {
            RateLimiter::hit($key, 600);
            return back()->withErrors(['code' => 'Code invalide.'])->withInput();
        }

        RateLimiter::clear($key);
        $request->session()->put('two_factor_verified', true);

        return redirect()->intended(route('superadmin.dashboard'));
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|string']);

        $user = $request->user();

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Mot de passe incorrect.']);
        }

        $user->two_factor_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        $request->session()->forget('two_factor_verified');

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'Authentification à deux facteurs désactivée.');
    }

    public function regenerateCodes(Request $request): RedirectResponse|View
    {
        $request->validate(['code' => 'required|digits:6']);

        $user = $request->user();

        $valid = (bool) $this->google2fa->verifyKey(
            $user->two_factor_secret,
            $request->code,
            1
        );

        if (! $valid) {
            return back()->withErrors(['code' => 'Code TOTP incorrect.']);
        }

        $recoveryCodes = $user->generateRecoveryCodes();

        return view('superadmin.2fa.recovery-codes', [
            'codes'        => $recoveryCodes,
            'regenerated'  => true,
        ]);
    }

    private function generateQrCodeSvg(string $data): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        return (new Writer($renderer))->writeString($data);
    }
}

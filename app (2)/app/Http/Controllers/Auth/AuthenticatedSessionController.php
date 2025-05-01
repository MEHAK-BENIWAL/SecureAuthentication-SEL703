<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuspiciousLoginAttempt;
use Illuminate\Validation\ValidationException;


class AuthenticatedSessionController extends Controller
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $this->client = new Client(config('app.twilio_account_id'), config('app.twilio_auth_token'));
        $this->from = "whatsapp:" . config('app.twilio_number');
    }
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $ip = $request->ip();
        $cacheKey = 'failed_login_' . md5($request->email . '|' . $ip);
        $attempts = Cache::get($cacheKey, 0);
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }
        $getUser = User::where('email', $request->email)->first();
        return redirect()->to(route('2fa', ['user_id' => base64_encode($getUser->id)]));
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

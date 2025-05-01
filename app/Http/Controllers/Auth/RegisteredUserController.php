<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Twilio\Rest\Client;

class RegisteredUserController extends Controller
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $this->client = new Client(config('app.twilio_account_id'), config('app.twilio_auth_token'));
        $this->from = "whatsapp:" . config('app.twilio_number');
    }
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Step 1: Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email'],
            'phone' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Step 2: Check for email or phone conflicts
        $checkEmail = User::where('email', $validated['email'])->first();
        $checkPhone = User::where('phone', $validated['phone'])->first();
        if ($checkEmail && $checkEmail->phone != $validated['phone']) {
            return back()->withErrors(['phone' => 'This phone number is already used with another email.'])->withInput();
        }

        if ($checkPhone && $checkPhone->email != $validated['email']) {
            return back()->withErrors(['email' => 'This email is already used with another phone number.'])->withInput();
        }

        // Step 3: Check if user already exists but not verified
        $existingUser = User::where([
            'email' => $validated['email'],
            'phone' => $validated['phone']
        ])->first();
        $otp = random_int(100000, 999999);
        
        if ($existingUser) {
            if($existingUser->otp_verified == 1){
                return back()->withErrors(['email' => 'You have already registered with this email address and phone. Please Login to continue'])->withInput();
            }
            $twilio = $this->sendSms('+61'.$request->phone, "Your OTP is: ".$otp);
            $existingUser->update(['otp' => $otp]);
            $userId = base64_encode($existingUser->id);
        } else {
            $twilio = $this->sendSms('+61'.$request->phone, "Your OTP is: ".$otp);
            $newUser = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'otp' => $otp,
            ]);
            
            event(new Registered($newUser));
            $userId = base64_encode($newUser->id);
        }
        // Step 4: Redirect to 2FA page
        return redirect()->route('2fa', ['user_id' => $userId]);
    }
    public function sendSms($to, $message)
    {
        $to = 'whatsapp:'.$to;
        $message = $this->client->messages->create($to, // to
            array(
            "from" => "whatsapp:+14155238886",
            "body" => $message
            )
        
      );
    }
}

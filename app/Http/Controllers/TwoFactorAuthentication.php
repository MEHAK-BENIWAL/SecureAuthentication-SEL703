<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\SuspiciousLoginAttempt;
use Illuminate\Validation\ValidationException;

class TwoFactorAuthentication extends Controller
{
    public function index(){
        return view('auth.otp');
    }
    public function verify_otp(Request $request){
        $ip = $request->ip();
        $otp = $request->digit1.''.$request->digit2.''.$request->digit3.''.$request->digit4.''.$request->digit5.''.$request->digit6;
        $params = ['otp_verified' => 1];
        $checkUserOtp = User::where(['id' => $request->user_id])->first();
        
        $cacheKey = 'failed_login_' . md5($request->email . '|' . $ip);
        $attempts = Cache::get($cacheKey, 0);
        if($checkUserOtp->otp != $otp){
            $attempts++;
            // Store updated count with expiration (e.g. 15 minutes)
            Cache::put($cacheKey, $attempts, now()->addMinutes(15));
            if ($attempts >= 5) {
                
                // Send mail logic here
                Mail::to($checkUserOtp->email)->send(new SuspiciousLoginAttempt($checkUserOtp->email, $ip, $attempts));
            }
            return back()->withErrors(['keystroke_data' => 'Invalid OTP'])->withInput();
        }
        Cache::forget($cacheKey);
        if($checkUserOtp->otp_verified != 1){
            $updateData = User::where('id', $request->user_id)->update($params);
        }
        $ipData = ['user_id' => $request->user_id, 'ip' => $request->ip()];
        $loginActivity = DB::table('login_activity')->insert($ipData);
        Auth::login($checkUserOtp);
        return redirect()->intended(route('dashboard'));
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\LoginOTP;
use App\Models\User;
use App\Services\OTPService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\Notification;

class AuthenticatedSessionController extends Controller
{
    protected $otpService;

    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }


    public function initiateLogin(Request $request)
    {
        // Validate inputs
        $request->validate([
            'login' => 'required', // can be phone or email
            'password' => 'required',
        ]);

        // Determine if login is email or phone
        $loginInput = $request->login;
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Find the user by email or phone
        $user = User::where($fieldType, $loginInput)->first();

        if (!$user) {
            session()->flash('status', ucfirst($fieldType) . ' not found');
            return redirect()->back();
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            session()->flash('status', 'Invalid credentials');
            return redirect()->back();
        }

        // If OTP is already verified, login directly
        if ($user->is_otp_verified > 0) {
            Auth::login($user);
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        $otp = '123123';
        $expiresAt = now()->addMinutes(10);

        // Store login attempt
        $loginAttempt = LoginOTP::create([
            'user_id' => $user->id,
            'phone' => $user->phone,
            'otp' => $otp,
            'expires_at' => $expiresAt,
        ]);

        // Send OTP
        try {
            $otpService = new OTPService();
            $otpService->sendOTP($user->phone, $otp);

            Notification::route('mail', $user->email)
                ->notify(new SendOtpNotification($otp));

            session()->flash('status', 'Use OTP 123123');

            return redirect()->route('login_otp', $loginAttempt->id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send OTP: ' . $e->getMessage()], 500);
        }
    }

    public function login_otp(Request $request){
        $data['temp_id'] = $request->temp_id;
        return view('auth.login_otp', $data);
    }

    public function verifyLoginOTP(Request $request)
    {
        $request->validate([
            'temp_id' => 'required',
            'otp' => 'required|numeric',
        ]);
        
        $loginAttempt = LoginOTP::find($request->temp_id);
        
        if (!$loginAttempt) {
            return response()->json(['error' => 'Invalid login attempt'], 400);
        }
        
        if ($loginAttempt->expires_at < now()) {
            return response()->json(['error' => 'OTP has expired'], 400);
        }
        
        if ($loginAttempt->otp != $request->otp) {
            return response()->json(['error' => 'Invalid OTP'], 400);
        }
        
        // OTP is valid, get the user
        $user = User::find($loginAttempt->user_id);
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        // Delete login attempt
        $loginAttempt->delete();
        
        // Log the user in
        if($user->prime_verified == User::PRIME_VERIFIED_STATUS_COMPLETED){
            $user->is_otp_verified = 1;
            $user->save();
        }
        Auth::login($user);
        
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
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

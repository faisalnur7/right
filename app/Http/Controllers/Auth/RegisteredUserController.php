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
use App\Models\Otp;
use App\Services\OTPService;
use Carbon\Carbon;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\Notification;

class RegisteredUserController extends Controller
{

    protected $otpService;

    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    public function otpPage($id){
        $data['otpData'] = OTP::query()->findOrFail($id);
        return view('auth.newUserOtp', $data);
    }

    public function initiateRegistration(Request $request)
    {
        // Validate inputs
        $request->validate([
            'phone' => 'required|unique:users,phone',
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'required|numeric|digits:11|unique:otps,phone',
        ]);
        
        $user = User::query()->where('phone',$request->phone)->first();
        if(!empty($user)){
            session()->flash('status', 'Phone number already taken');
            return redirect()->back();
        }
        // Generate OTP
        $otp = rand(100000, 999999);
        $otp = '123123';
        $expiresAt = Carbon::now()->addMinutes(10);
        
        // Store temporary registration data
        $tempRegistration = Otp::create([
            'phone' => $request->phone,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'otp' => $otp,
            'expires_at' => $expiresAt, // OTP expires in 10 minutes
        ]);
        
        // Send OTP
        try {
            $otpService = new OTPService();
            // $this->otpService->sendOTP($request->phone, $otp);

            // Notification::route('mail', $request->email)->notify(new SendOtpNotification($otp));

            session()->flash('status', 'Use OTP 123123');
            
            return redirect()->route('user.otpPage', $tempRegistration->id);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send OTP: ' . $e->getMessage()], 500);
        }
    }

    public function newUserVerify(Request $request){
        $request->validate([
            'otp' => 'required|numeric',
            'phone' => 'required',
            'email' => 'required',
        ]);

        $tempRegistration = Otp::query()->where('otp',$request->otp)->where('phone',$request->phone)->where('email',$request->email)->first();
        
        if (!$tempRegistration) {
            return response()->json(['error' => 'Invalid registration attempt'], 400);
        }
        
        if ($tempRegistration->expires_at < now()) {
            return response()->json(['error' => 'OTP has expired'], 400);
        }
        
        if ($tempRegistration->otp != $request->otp) {
            return response()->json(['error' => 'Invalid OTP'], 400);
        }

        $user = User::create([
            'name' => $tempRegistration->name,
            'email' => $tempRegistration->email,
            'phone' => $tempRegistration->phone,
            'is_active' => User::INACTIVE,
            'password' => $tempRegistration->password,
            'phone_verified_at' => Carbon::now(),
        ]);

        event(new Registered($user));

        $tempRegistration->delete();

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));


    }

    public function otp(Request $request){
        $data['temp_id'] = $request->temp_id;
        return view('auth.otp', $data);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'temp_id' => 'required',
            'otp' => 'required|numeric',
        ]);
        
        $tempRegistration = Otp::find($request->temp_id);
        
        if (!$tempRegistration) {
            return response()->json(['error' => 'Invalid registration attempt'], 400);
        }
        
        if ($tempRegistration->expires_at < now()) {
            return response()->json(['error' => 'OTP has expired'], 400);
        }
        
        if ($tempRegistration->otp != $request->otp) {
            return response()->json(['error' => 'Invalid OTP'], 400);
        }

        $user = User::create([
            'name' => $tempRegistration->name,
            'email' => $tempRegistration->email,
            'phone' => $tempRegistration->phone,
            'is_active' => User::INACTIVE,
            'password' => $tempRegistration->password,
            'phone_verified_at' => Carbon::now(),
            'user_type' => User::CUSTOMER
        ]);

        event(new Registered($user));

        $tempRegistration->delete();

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Otp;
use App\Models\User;
use App\Models\Division;
use App\Models\District;
use App\Models\PoliceStation;
use App\Models\PostOffice;
use App\Models\Kyc;
use App\Models\NomineeInfo;
use App\Models\Wallet;
use App\Models\PackageUser;
use App\Services\OTPService;
use Carbon\Carbon;
use App\Notifications\SendOtpNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    protected $otpService;

    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function index()
    {
        $data['users'] = User::paginate(10);
        return view('admin.user.index', $data);
    }

    public function otp(Request $request){
        $request->validate([
            'phone' => 'required|numeric|digits:11|unique:otps,phone',
        ]);

        $otp = rand(100000, 999999);
        $otp = '123123';
        $expiresAt = Carbon::now()->addMinutes(10);

        Otp::updateOrCreate(
            ['phone' => $request->phone],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        $this->otpService->sendOTP($request->phone, $otp);
        Notification::route('mail', $request->email)->notify(new SendOtpNotification($otp));
        session()->flash('status', 'Use OTP 123123');

        return redirect()->route('verify_otp');
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|digits:11|exists:otps,phone',
            'email' => 'nullable|email'
        ]);

        // Generate a new OTP
        $otp = rand(100000, 999999);
        $otp = '123123'; // For testing, hardcoded. Remove in production.
        $expiresAt = Carbon::now()->addMinutes(10);

        // Update existing OTP record
        Otp::updateOrCreate(
            ['phone' => $request->phone],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        // Send OTP via SMS or any service
        $this->otpService->sendOTP($request->phone, $otp);

        // Optionally send email notification
        if ($request->filled('email')) {
            Notification::route('mail', $request->email)
                ->notify(new SendOtpNotification($otp));
        }

        session()->flash('status', 'OTP resent successfully');
        return redirect()->route('verify_otp');
    }


    public function verify_otp(Request $request){
        return view('auth.otp');
    }

    public function verify(Request $request){
        $otp = $request->otp;

        $otpObj = Otp::query()->where('otp',$otp)->first();

        if(empty($otpObj)){
            session()->flash('status','OTP Expired!!!');
            return redirect()->back();
        }
        if($otpObj->otp != $otp){
            session()->flash('status','Wrong OTP!!!');
            return redirect()->back();
        }
    }

    public function editUserProfile(){
        $data['division_ids'] = Division::all();
        $data['district_ids'] = District::all();
        $data['police_station_ids'] = PoliceStation::all();
        $data['post_office_ids'] = PostOffice::all();

        return view('user.profile.edit_profile', $data);
    }
    
    public function updateUserProfile(){
        
    }


    public function auto_user_add(Request $request, $quantity = null)
    {
        $quantity = $quantity ?? 1;

        // Get all user IDs for reference_user_id
        $user_ids = User::pluck('id')->toArray();

        $createdUsers = [];

        DB::beginTransaction();
        try {
            for ($i = 0; $i < $quantity; $i++) {
                // Pick a random user ID for reference_user_id
                $randomRefUserId = $user_ids ? $user_ids[array_rand($user_ids)] : null;

                $refUser = User::find($randomRefUserId);
                // Generate random user data
                $user_data = [
                    'name' => 'User_' . Str::random(6),
                    'email' => Str::random(8) . '@example.com',
                    'phone' => '01' . rand(300000000, 999999999),
                    'password' => Hash::make('123123'),
                    'is_active' => 1,
                    'phone_verified_at' => now(),
                    'user_type' => null,
                    'user_affiliate_type' => 2,
                    'reference_id' => $refUser->kyc->affiliate_id,
                    'reference_user_id' => $randomRefUserId,
                    'temp_reference_user_id' => null,
                    'district_id' => 8,
                    'police_station_id' => 67,
                    'post_office_id' => 644,
                    'postal_code' => 4324,
                    'is_super_prime' => 0,
                    'prime_verified'=> 6
                ];

                // Create user
                $newUser = User::create($user_data);
                $newUser->prime_verified = 6;
                $newUser->save();

                // Create related KYC
                $kyc_data = [
                    'user_id' => $newUser->id,
                    'referer_id' => $randomRefUserId,
                    'affiliate_id' => $this->create_affiliate_id($randomRefUserId),
                    'doc_type' => 1,
                    'document_file' => null,
                    'father' => 'Father_' . Str::random(5),
                    'mother' => 'Mother_' . Str::random(5),
                    'dob' => now()->subYears(rand(18, 40))->toDateString(),
                    'permanent_division_id' => 1,
                    'permanent_district_id' => 1,
                    'permanent_police_station_id' => 1,
                    'permanent_post_office_id' => 1,
                    'permanent_post_code' => 1234,
                    'present_division_id' => 1,
                    'present_district_id' => 1,
                    'present_police_station_id' => 1,
                    'present_post_office_id' => 1,
                    'present_post_code' => 1234,
                    'account_type' => 1,
                    'account_number' => rand(10000000, 99999999),
                    'emergency_contact' => '01' . rand(300000000, 999999999),
                    'present_address' => 'Present Address ' . Str::random(5),
                    'permanent_address' => 'Permanent Address ' . Str::random(5),
                    'is_same_address' => 1,
                    'postal_code' => 4324,
                    'transaction_number' => rand(1000000000, 9999999999),
                    'transaction_mobile_number' => '01' . rand(300000000, 999999999),
                    'payment_type' => 'bkash',
                    'nominee_name' => 'Nominee_' . Str::random(5),
                    'nominee_nid' => rand(1000000000, 9999999999),
                    'relation' => 'Brother',
                    'package' => 'Basic',
                    'photo' => null,
                    'document_number' => rand(1000000, 9999999),
                ];

                Kyc::create($kyc_data);
                NomineeInfo::create($kyc_data);

                // Create two wallets for the user
                Wallet::create([
                    'user_id' => $newUser->id,
                    'type'    => 'prime',
                    'balance' => 0,
                ]);

                Wallet::create([
                    'user_id' => $newUser->id,
                    'type'    => 'affiliate',
                    'balance' => 0,
                ]);

                // Create PackageUser record
                PackageUser::create([
                    'user_id'                  => $newUser->id,
                    'subscription_package_id'  => 1, // or pick random/default package
                    'payment_option_id'        => 1, // default payment option
                    'transaction_number'       => rand(1000000000, 9999999999),
                    'transaction_mobile_number'=> '01' . rand(300000000, 999999999),
                    'amount'                   => 1000, // example fixed amount
                    'assigned_at'              => now(),
                    'expires_at'               => now()->addMonths(6),
                    'status'                   => 1, // active
                    'is_verified'              => 1,
                ]);

                $createdUsers[] = $newUser;
            }

            DB::commit();

            return response()->json([
                'message' => "$quantity user(s) with KYC, Wallets, and PackageUser created successfully",
                'users' => $createdUsers
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Failed to create users',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function create_affiliate_id($randomRefUserId){
        $user = User::findOrFail($randomRefUserId);
        $refered_user_count = User::query()->where('reference_user_id',$user->id)->count();
        $reference_id = 'RB-'.sprintf('%04.3d', $user->id).'-'.sprintf('%04.3d', $refered_user_count+1);
        return $reference_id;
    }

}

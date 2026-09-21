<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const ACTIVE = 1;
    const INACTIVE = 0;

    const USER_ADVANCE_INACTIVE = 0;
    const USER_ADVANCE_ACTIVE = 1;

    // User Type
    const ADMIN = 1;
    const MERCHANT = 2;
    const CUSTOMER = 3;

    // Affiliate type
    const GENERAL = 1;
    const PRIME = 2;

    const PRIME_VERIFIED_STATUS_REF_INCOMPLETED = 0;
    const PRIME_VERIFIED_STATUS_VERIFIED        = 1;
    const PRIME_VERIFIED_STATUS_POSTAL_ASSIGN   = 2;
    const PRIME_VERIFIED_STATUS_NOMINEE         = 3;
    const PRIME_VERIFIED_STATUS_PACKAGE         = 4;
    const PRIME_VERIFIED_STATUS_PAYMENT         = 5;
    const PRIME_VERIFIED_STATUS_COMPLETED       = 6;

    const PRIME_STATUS = [
        'Reference'             => self::PRIME_VERIFIED_STATUS_VERIFIED,
        'Postal Info'           => self::PRIME_VERIFIED_STATUS_POSTAL_ASSIGN,
        'Nominee'               => self::PRIME_VERIFIED_STATUS_NOMINEE,
        'Package'               => self::PRIME_VERIFIED_STATUS_PACKAGE,
        'Payment'               => self::PRIME_VERIFIED_STATUS_PAYMENT,
    ];


    const USER_TYPES = [
        'Admin' => self::ADMIN,
        'Merchant' => self::MERCHANT,
        'Customer' => self::CUSTOMER
    ];

    const AFFILIATE_TYPES = [
        'General' => self::GENERAL,
        'Prime' => self::PRIME
    ];

    const USER_PACKAGE_INACTIVE = 0;
    const USER_PACKAGE_ACTIVE = 1;
    const USER_PACKAGE_PENDING = 2;

    const PRIME_SUBUSER_LIMIT = 14;

    const FATHER = "Father";
    const MOTHER = "Mother";
    const SISTER = "Sister";
    const BROTHER = "Brother";
    const SON = "Son";
    const DAUGHTER = "Daughter";
    const HUSBAND = "Husband";
    const WIFE = "Wife";
    const OTHER = "Other";

    const RELATION = [
        'Father'   => self::FATHER,
        'Mother'   => self::MOTHER,
        'Sister'   => self::SISTER,
        'Brother'  => self::BROTHER,
        'Son'      => self::SON,
        'Daughter' => self::DAUGHTER,
        'Husband'  => self::HUSBAND,
        'Wife'     => self::WIFE,
        'Other'    => self::OTHER,
    ];



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
        'advance_active',
        'remaining_days',
        'phone_verified_at',
        'user_type',
        'user_affiliate_type',
        'reference_id',
        'reference_user_id',
        'temp_reference_user_id',
        'district_id',
        'police_station_id',
        'post_office_id',
        'postal_code',
        'is_super_prime',
        'is_otp_verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function kyc(){
        return $this->hasOne(Kyc::class,'user_id');
    }

    public function prime_request(){
        return $this->hasOne(PrimeRequest::class,'requester_id');
    }

    public function district(){
        return $this->belongsTo(District::class,'district_id');
    }

    public function police_station(){
        return $this->belongsTo(PoliceStation::class,'police_station_id');
    }

    public function post_office(){
        return $this->belongsTo(PostOffice::class,'post_office_id');
    }

    public function nominee(){
        return $this->hasOne(NomineeInfo::class,'user_id');
    }

    public function packages(){
        return $this->belongsToMany(
            SubscriptionPackage::class,
            'package_users',              // correct pivot table
            'user_id',                    // foreign key on pivot pointing to users
            'subscription_package_id'     // foreign key on pivot pointing to subscription_packages
        )->withPivot([
            'id',
            'payment_option_id',
            'transaction_number',
            'transaction_mobile_number',
            'amount',
            'assigned_at',
            'expires_at',
            'status',
            'is_verified',
        ]);
    }

    public function activePackage()
    {
        return $this->belongsToMany(
            SubscriptionPackage::class,
            'package_users',              // Pivot table
            'user_id',                    // Foreign key on pivot pointing to users
            'subscription_package_id'     // Foreign key on pivot pointing to subscription_packages
        )->withPivot([
            'id',
            'payment_option_id',
            'transaction_number',
            'transaction_mobile_number',
            'amount',
            'assigned_at',
            'expires_at',
            'status',
            'is_verified',
        ])->wherePivot('status', 1); // ✅ Only include active subscriptions
    }


    public function cart(){
        return $this->hasOne(PrimeCart::class);
    }

    public function addresses(){
        return $this->hasMany(Address::class)->where('type', Address::BILLING_ADDRESS_TYPE);
    }

    public function shippingAddresses(){
        return $this->hasMany(Address::class)->where('type', Address::SHIPPING_ADDRESS_TYPE);
    }

    public function getRemainingUnits($saleLogId){
        return $this->saleLogUnits()
            ->where('sale_log_id', $saleLogId)
            ->value('remaining_units') ?? 0;
    }

    public function saleLogUnits(){
        return $this->hasMany(UserSaleLogUnit::class);
    }

    public function isActiveInSaleLog($saleLogId)
    {
        return $this->saleLogUnits()
            ->where('sale_log_id', $saleLogId)
            ->where('is_active', 1)
            ->exists();
    }

    public function isActiveOrPendingInSaleLog($saleLogId){
        $referenceUser = $this->referenceUser;
        $latestNode = $referenceUser->binaryTreeNodes()->where('sale_log_id', $saleLogId)->latest()->first();

        if($latestNode && ($latestNode->status = 1 && $latestNode->status = 1)){
            return 1;
        }

        if($latestNode && ($latestNode->status = 0 && $latestNode->status = 1)){
            return 2;
        }
        
        if($latestNode && ($latestNode->status = 0 && $latestNode->status = 0)){
            return 0;
        }
    }

    public function isActiveInSaleLogTree($saleLogId){
        return $this->binaryTreeNodes()
            ->where('sale_log_id', $saleLogId)
            ->where('status', 1)
            ->latest()
            ->first();
    }

    public function isActiveInSaleLogTreeAdmin($saleLogId){
        $latestNode =  $this->binaryTreeNodes()
            ->where('sale_log_id', $saleLogId)
            ->latest()
            ->first();

        if(!empty($latestNode)){
            if ($latestNode->status == 0 && $latestNode->active_in_sale_log == 0) {
                return BinaryTreeNode::INACTIVE_IN_TREE;
            }

            if ($latestNode->status == 1 && $latestNode->active_in_sale_log == 1) {
                return BinaryTreeNode::ACTIVE_IN_TREE;
            }

            if ($latestNode->status == 0 && $latestNode->active_in_sale_log == 1) {
                return BinaryTreeNode::INACTIVE_PENDING_IN_TREE;
            }
        }else{
            return BinaryTreeNode::INACTIVE_IN_TREE;
        }
    }

    public function maxActivationInSaleLog($saleLogId){
        $latestNode =  $this->binaryTreeNodes()
            ->where('sale_log_id', $saleLogId)
            ->latest()
            ->first();

        $activation_number = !empty($latestNode) ? $latestNode->activation_number : 0;

        return $activation_number;
    }

    // Wallet functions

    public function wallets(){
        return $this->hasMany(Wallet::class);
    }

    public function primeWallet(){
        return $this->hasOne(Wallet::class)->where('type', 'prime');
    }

    public function affiliateWallet(){
        return $this->hasOne(Wallet::class)->where('type', 'affiliate');
    }

    public function transactions()
    {
        return $this->hasMany(PrimeTransaction::class,'user_id');
    }

    public function referenceUser()
    {
        return $this->belongsTo(User::class, 'reference_user_id');
    }

    public function getReferenceUserOfReferrerAttribute()
    {
        return $this->referenceUser?->referenceUser;
    }

    public function binaryTreeNodes()
    {
        return $this->hasMany(BinaryTreeNode::class);
    }

    public function adminReferenceRequest(){
        return $this->hasMany(AdminReferenceRequest::class);
    }

    public function disbursements(){
        return $this->hasMany(Disbursement::class);
    }


    public function getIncomeByDateRange($from = null, $to = null)
    {
        // Default to today if no date range provided
        $from = $from ? Carbon::parse($from)->startOfDay() : Carbon::today()->startOfDay();
        $to   = $to ? Carbon::parse($to)->endOfDay() : Carbon::today()->endOfDay();

        $transactions = $this->transactions()
            ->whereBetween('created_at', [$from, $to]);

        return [
            'Leads' => $transactions->clone()
                ->whereIn('type', [
                    PrimeTransaction::DIRECT_PRODUCT_USE_COMMISSION,
                    PrimeTransaction::INDIRECT_PRODUCT_USE_COMMISSION,
                ])
                ->sum('amount_in'),

            'Affiliate' => $transactions->clone()
                ->whereIn('type', [
                    PrimeTransaction::DIRECT_PRODUCT_PURCHASE_COMMISSION,
                    PrimeTransaction::INDIRECT_PRODUCT_PURCHASE_COMMISSION,
                ])
                ->sum('amount_in'),

            'Subscription' => $transactions->clone()
                ->whereIn('type', [
                    PrimeTransaction::DIRECT_SUBSCRIPTION_COMMISSION,
                    PrimeTransaction::INDIRECT_SUBSCRIPTION_COMMISSION,
                ])
                ->sum('amount_in'),

            'Associate' => $transactions->clone()
                ->where('type', PrimeTransaction::ASSOCIATE_COMMISSION)
                ->sum('amount_in'),

            'Total' => $transactions->clone()
                ->sum('amount_in'),
        ];
    }

    public function getIncomeByDate($date = null)
    {
        // Default to today if no date provided
        $date = $date ? Carbon::parse($date) : Carbon::today();

        $from = $date->copy()->startOfDay();
        $to   = $date->copy()->endOfDay();

        $transactions = $this->transactions()
            ->whereBetween('created_at', [$from, $to]);

        return [
            'Leads' => (clone $transactions)
                ->whereIn('type', [
                    PrimeTransaction::DIRECT_PRODUCT_USE_COMMISSION,
                    PrimeTransaction::INDIRECT_PRODUCT_USE_COMMISSION,
                ])
                ->sum('amount_in'),

            'Affiliate' => (clone $transactions)
                ->whereIn('type', [
                    PrimeTransaction::DIRECT_PRODUCT_PURCHASE_COMMISSION,
                    PrimeTransaction::INDIRECT_PRODUCT_PURCHASE_COMMISSION,
                ])
                ->sum('amount_in'),

            'Subscription' => (clone $transactions)
                ->whereIn('type', [
                    PrimeTransaction::DIRECT_SUBSCRIPTION_COMMISSION,
                    PrimeTransaction::INDIRECT_SUBSCRIPTION_COMMISSION,
                ])
                ->sum('amount_in'),

            'Associate' => (clone $transactions)
                ->where('type', PrimeTransaction::ASSOCIATE_COMMISSION)
                ->sum('amount_in'),

            'Total' => $transactions->sum('amount_in'),
        ];
    }

    public function getDisbursementByDate($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        // Fetch incomes once
        $incomes = $this->getIncomeByDate($date);

        // Fetch disbursements in a single query
        $disbursements = $this->disbursements()
            ->whereDate('disburse_date', $date)
            ->selectRaw('
                COALESCE(SUM(total_amount), 0) as total,
                COALESCE(SUM(leads_amount), 0) as leads,
                COALESCE(SUM(affiliate_amount), 0) as affiliate,
                COALESCE(SUM(subscription_amount), 0) as subscription,
                COALESCE(SUM(associate_amount), 0) as associate
            ')
            ->first();

        return [
            'leads'        => $incomes['Leads']        - $disbursements->leads,
            'affiliate'    => $incomes['Affiliate']    - $disbursements->affiliate,
            'subscription' => $incomes['Subscription'] - $disbursements->subscription,
            'associate'    => $incomes['Associate']    - $disbursements->associate,
            'total'        => $incomes['Total']        - $disbursements->total,
        ];
    }

    public function activationCountBySaleLog($saleLogId)
    {
        return $this->binaryTreeNodes()->where('user_id', $this->id)->where('sale_log_id', $saleLogId)->count();
    }



}

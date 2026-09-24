<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\ProductListController;
use App\Http\Controllers\PrimeCartController;
use App\Http\Controllers\PrimeCheckoutController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PrimeRequestController;
use App\Http\Controllers\PrimeOrderController;
use App\Http\Controllers\PrimeStockController;
use App\Http\Controllers\UseProductController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\PrimeTransactionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserActiveRequestController;
use App\Http\Controllers\BinaryTreeNodeController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', [DashboardController::class, 'index'])->name('homepage');
Route::get('/', [DashboardController::class, 'index'])->name('homepage');

Route::get('/reboot', function () {
    Artisan::call('optimize:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');
    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('view:cache');
    return 'rebooted & caches cleared!';
});

Route::get('/update_tree', function () {
    Artisan::call('update:user-sale-log');
    return 'Inactive user updated in tree';
});
Route::get('/auto_user_add/{quantity?}', [UserController::class, 'auto_user_add'])->name('auto_user_add');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
});

require __DIR__.'/auth.php';

Route::get('/user/otpPage/{id}', [RegisteredUserController::class, 'otpPage'])->name('user.otpPage');
Route::post('/user/newUserVerify', [RegisteredUserController::class, 'newUserVerify'])->name('newUserVerify');

Route::post('/user/otp', [RegisteredUserController::class, 'initiateRegistration'])->name('user.otp');
Route::get('/user/otp/{temp_id}', [RegisteredUserController::class, 'otp'])->name('otp');
Route::post('/user/verify', [RegisteredUserController::class, 'store'])->name('verify');

Route::post('/login/otp', [AuthenticatedSessionController::class, 'initiateLogin'])->name('login.otp');
Route::get('/login/otp/{temp_id}', [AuthenticatedSessionController::class, 'login_otp'])->name('login_otp');
Route::post('/login/verify', [AuthenticatedSessionController::class, 'verifyLoginOTP'])->name('login_verify');

Route::post('/resend-otp', [UserController::class, 'resendOtp'])->name('resend.otp');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/user/list', [UserController::class, 'index'])->name('user.list');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::get('/user/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::patch('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('user.destroy');

    Route::get('/profile/settings', [UserController::class, 'editUserProfile'])->name('editUserProfile');
    Route::post('/update/profile/settings', [UserController::class, 'updateUserProfile'])->name('updateUserProfile');

    // Role

    Route::get('/role/list', [RoleController::class, 'index'])->name('role.list');
    Route::get('/role/create', [RoleController::class, 'create'])->name('role.create');
    Route::get('/role/store', [RoleController::class, 'store'])->name('role.store');
    Route::get('/role/edit/{id}', [RoleController::class, 'edit'])->name('role.edit');
    Route::patch('/role/update/{id}', [RoleController::class, 'update'])->name('role.update');
    Route::delete('/role/delete/{id}', [RoleController::class, 'destroy'])->name('role.destroy');

    // KYC 

    Route::prefix('kyc')->group(function () {
        Route::get('/add', [KycController::class, 'index'])->name('kyc.list');
        Route::get('/prime', [KycController::class, 'prime'])->name('kyc.prime');
        Route::post('/prime_store', [KycController::class, 'prime_store'])->name('kyc.prime_store');
        Route::get('/create', [KycController::class, 'create'])->name('kyc.create');
        Route::post('/store', [KycController::class, 'store'])->name('kyc.store');
        Route::get('/edit/{id}', [KycController::class, 'edit'])->name('kyc.edit');
        Route::post('/update/{id}', [KycController::class, 'update'])->name('kyc.update');
        Route::delete('/delete/{id}', [KycController::class, 'destroy'])->name('kyc.delete');

        Route::post('assign_postal_area',[KycController::class,'assign_postal_area'])->name('assign_postal_area');
        Route::post('nominee',[KycController::class,'nominee'])->name('nominee');
        Route::post('choose_package',[KycController::class,'choose_package'])->name('choose_package');
        Route::post('finish_payment',[KycController::class,'finish_payment'])->name('finish_payment');
        Route::post('/adminRequest', [KycController::class, 'adminRequest'])->name('kyc.adminRequest');

    });

    Route::get('/prime_requests', [PrimeRequestController::class, 'prime_requests'])->name('prime_requests');
    Route::get('/approved_prime_requests', [PrimeRequestController::class, 'approved_prime_requests'])->name('approved_prime_requests');
    Route::get('/rejected_prime_requests', [PrimeRequestController::class, 'rejected_prime_requests'])->name('rejected_prime_requests');
    Route::get('/general_affiliates', [PrimeRequestController::class, 'general_affiliates'])->name('general_affiliates');
    Route::post('/prime-request', [PrimeRequestController::class, 'store'])->name('prime.request');
    Route::post('/prime-request-cancel', [PrimeRequestController::class, 'cancel_request'])->name('prime.cancel_request');
    Route::post('/prime-request/{id}/respond', [PrimeRequestController::class, 'respond'])->name('prime.respond');

    // Product List for Prime users
    Route::get('/products', [ProductListController::class, 'products'])->name('products');
    Route::get('/product_details/{id}/{log_id}', [ProductListController::class, 'product_details'])->name('product_details');
    Route::get('/products/filter', [ProductListController::class, 'filterProducts'])->name('products.filter');

    Route::post('/add_to_cart', [PrimeCartController::class, 'add_to_cart'])->name('add_to_cart');
    Route::post('/remove_item', [PrimeCartController::class, 'remove_item'])->name('remove_item');

    Route::get('/cart', [PrimeCartController::class, 'cart'])->name('cart');
    Route::post('/update_item_qty', [PrimeCartController::class, 'update_item_qty'])->name('update_item_qty');
    Route::post('/get_shipping_cost', [PrimeCartController::class, 'get_shipping_cost'])->name('get_shipping_cost');

    // Checkout page
    Route::get('/order_success/{order_id}', [PrimeCheckoutController::class, 'order_success'])->name('order_success');
    Route::get('/prime_checkout', [PrimeCheckoutController::class, 'showCheckout'])->name('prime_checkout');
    Route::post('/prime_checkout', [PrimeCheckoutController::class, 'placeOrder'])->name('prime_checkout.place_order');
    Route::get('/prime_orders', [PrimeOrderController::class, 'prime_orders'])->name('prime_orders');
    Route::get('/prime_order_details/{id}', [PrimeOrderController::class, 'prime_order_details'])->name('prime_order_details');

    // Address
    Route::post('/prime_user_address_store', [AddressController::class, 'store'])->name('prime_user.address.store');
    Route::post('/address-book', [AddressController::class, 'destroy'])->name('address-book.destroy');

    // Sale/Use Product
    Route::get('/prime_stock', [PrimeStockController::class, 'prime_stock'])->name('prime_stock');
    Route::post('/stock_sale', [UseProductController::class, 'stock_sale'])->name('stock_sale');
    Route::get('/stock_sale_log', [UseProductController::class, 'stock_sale_log'])->name('stock_sale_log');
    Route::get('/used_product', [UseProductController::class, 'used_product'])->name('used_product');
    Route::get('/sold_product', [UseProductController::class, 'sold_product'])->name('sold_product');
    Route::post('/use_product/{product_id}/{sale_log_id}', [BinaryTreeNodeController::class, 'useProduct'])->name('use_product');


    Route::get('/prime_transactions', [PrimeTransactionController::class, 'prime_transactions'])->name('prime_transactions');

    Route::post('/user-active-requests', [UserActiveRequestController::class, 'store'])->name('user-active-requests.store');
    Route::put('/user-active-requests/{productSaleLog}', [UserActiveRequestController::class, 'update'])->name('user-active-requests.update');
    Route::delete('/user-active-requests/{productSaleLog}', [UserActiveRequestController::class, 'destroy'])->name('user-active-requests.destroy');

    Route::get('/notifications/{id}/redirect', [NotificationController::class, 'redirect'])->name('notifications.redirect');
});

// AJAX calls
Route::get('load_affiliate_id', [CommonController::class,'load_affiliate_id'])->name('load_affiliate_id');
Route::get('load_districts', [CommonController::class,'load_districts'])->name('load_districts');
Route::get('load_police_stations',[CommonController::class,'load_police_stations'])->name('load_police_stations');
Route::get('load_post_offices',[CommonController::class,'load_post_offices'])->name('load_post_offices');
Route::get('/get_user_sale_info', [CommonController::class, 'get_user_sale_info'])->name('get_user_sale_info');

Route::get('/notifications', [CommonController::class, 'notifications'])->name('notifications.index');
Route::get('/notifications/unread', [CommonController::class, 'unread_notifications'])->name('notifications.unread');
Route::get('/notifications/unread_user', [CommonController::class, 'unread_notifications_user'])->name('notifications.unread.user');



require __DIR__.'/merchant-auth.php';
require __DIR__.'/admin-auth.php';
require __DIR__.'/admin-routes.php';
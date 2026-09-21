<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SaleLogController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SubscriptionPackageController;
use App\Http\Controllers\PackageFeatureController;
use App\Http\Controllers\PaymentOptionController;
use App\Http\Controllers\SubscriptionRequestController;
use App\Http\Controllers\ShippingRuleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\PoliceStationController;
use App\Http\Controllers\PostOfficeController;
use App\Http\Controllers\BinaryTreeNodeController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\AdminReferenceRequestController;
use App\Http\Controllers\AdminActiveReferenceRequestController;
use App\Http\Controllers\DisbursementController;
use App\Http\Controllers\PrimeActiveRequestController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->middleware('auth:admin')->group(function () {
    
    Route::get('/notifications/{id}/redirect', [AdminNotificationController::class, 'redirect'])->name('admin.notifications.redirect');
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/adminTreeSelect', [BinaryTreeNodeController::class, 'adminTreeSelect'])->name('adminTreeSelect');
    Route::get('/adminTreeView', [BinaryTreeNodeController::class, 'adminTreeView'])->name('adminTreeView');
    Route::get('/binary-tree/user/{user}/{nodeId}', [BinaryTreeNodeController::class, 'adminTreeFromUser'])->name('admin.tree.from_user');
    Route::get('/tree/filter', [BinaryTreeNodeController::class, 'adminTreeFilter'])->name('admin.tree.filtered');

    Route::post('/adminUseProduct/{user_id}/{product_id}/{sale_log_id}', [BinaryTreeNodeController::class, 'adminUseProduct'])->name('adminUseProduct');
    Route::get('/adminUserList', [BinaryTreeNodeController::class, 'adminUserList'])->name('adminUserList');
    Route::get('/adminUserStock/{user_id}', [BinaryTreeNodeController::class, 'adminUserStock'])->name('adminUserStock');

    
    // Subscription Package Routes

    Route::prefix('subscription-management')->group(function () {

        Route::prefix('subscription_request')->group(function () {
            Route::get('/pending_list', [SubscriptionRequestController::class, 'pending_list'])->name('pending_list');
            Route::post('/respond/{id}', [SubscriptionRequestController::class, 'respond'])->name('admin.respond');
        });


        Route::prefix('subscription_package')->group(function () {
            Route::get('/list', [SubscriptionPackageController::class, 'index'])->name('subscription_package.list');
            Route::get('/create', [SubscriptionPackageController::class, 'create'])->name('subscription_package.create');
            Route::post('/store', [SubscriptionPackageController::class, 'store'])->name('subscription_package.store');
            Route::get('/edit/{id}', [SubscriptionPackageController::class, 'edit'])->name('subscription_package.edit');
            Route::post('/update/{id}', [SubscriptionPackageController::class, 'update'])->name('subscription_package.update');
            Route::delete('/delete/{id}', [SubscriptionPackageController::class, 'destroy'])->name('subscription_package.delete');
        });

        Route::prefix('package_feature')->group(function () {
            Route::get('/list', [PackageFeatureController::class, 'index'])->name('package_feature.list');
            Route::get('/create', [PackageFeatureController::class, 'create'])->name('package_feature.create');
            Route::post('/store', [PackageFeatureController::class, 'store'])->name('package_feature.store');
            Route::get('/edit/{id}', [PackageFeatureController::class, 'edit'])->name('package_feature.edit');
            Route::post('/update/{id}', [PackageFeatureController::class, 'update'])->name('package_feature.update');
            Route::delete('/delete/{id}', [PackageFeatureController::class, 'destroy'])->name('package_feature.delete');
        });

        Route::prefix('payment_option')->group(function () {
            Route::get('/list', [PaymentOptionController::class, 'index'])->name('payment_option.list');
            Route::get('/create', [PaymentOptionController::class, 'create'])->name('payment_option.create');
            Route::post('/store', [PaymentOptionController::class, 'store'])->name('payment_option.store');
            Route::get('/edit/{id}', [PaymentOptionController::class, 'edit'])->name('payment_option.edit');
            Route::post('/update/{id}', [PaymentOptionController::class, 'update'])->name('payment_option.update');
            Route::delete('/delete/{id}', [PaymentOptionController::class, 'destroy'])->name('payment_option.delete');
        });

        Route::prefix('admin_reference_request')->group(function () {
            Route::get('/list', [AdminReferenceRequestController::class, 'index'])->name('admin_reference_request.list');
            Route::get('/create', [AdminReferenceRequestController::class, 'create'])->name('admin_reference_request.create');
            Route::post('/store', [AdminReferenceRequestController::class, 'store'])->name('admin_reference_request.store');
            Route::get('/edit/{id}', [AdminReferenceRequestController::class, 'edit'])->name('admin_reference_request.edit');
            Route::post('/update/{id}', [AdminReferenceRequestController::class, 'update'])->name('admin_reference_request.update');
            Route::delete('/delete/{id}', [AdminReferenceRequestController::class, 'destroy'])->name('admin_reference_request.delete');
            Route::post('/approve/{id}', [AdminReferenceRequestController::class, 'approve'])->name('admin_reference_request.approve');
        });

        Route::prefix('prime_active_request')->group(function () {
            Route::get('/list', [PrimeActiveRequestController::class, 'index'])->name('prime_active_request.list');
            Route::get('/create', [PrimeActiveRequestController::class, 'create'])->name('prime_active_request.create');
            Route::post('/store', [PrimeActiveRequestController::class, 'store'])->name('prime_active_request.store');
            Route::get('/edit/{id}', [PrimeActiveRequestController::class, 'edit'])->name('prime_active_request.edit');
            Route::post('/update/{id}', [PrimeActiveRequestController::class, 'update'])->name('prime_active_request.update');
            Route::delete('/delete/{id}', [PrimeActiveRequestController::class, 'destroy'])->name('prime_active_request.delete');
            Route::post('/approve/{id}', [PrimeActiveRequestController::class, 'approve'])->name('prime_active_request.approve');
            Route::post('/reject/{id}', [PrimeActiveRequestController::class, 'reject'])->name('prime_active_request.reject');
        });
        
    });

    Route::prefix('disbursement-management')->group(function () {

        Route::prefix('users')->group(function () {
            Route::get('/disbursement_list', [DisbursementController::class, 'disbursement_list'])->name('disbursement_list');
            Route::get('/disbursement_list_completed', [DisbursementController::class, 'disbursement_list_completed'])->name('disbursement_list_completed');
            Route::post('/disbursement_bulk', [DisbursementController::class, 'bulkDisburse'])->name('admin.disbursement.bulk');
        });
    });

    // Product Management
    Route::prefix('product-management')->group(function () {

        // Product Routes
        Route::prefix('products')->group(function () {
            Route::get('/list', [ProductController::class, 'index'])->name('product.list');
            Route::get('/show/{id}', [ProductController::class, 'show'])->name('product.show');
            Route::get('/create', [ProductController::class, 'create'])->name('product.create');
            Route::post('/store', [ProductController::class, 'store'])->name('product.store');
            Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
            Route::post('/update/{id}', [ProductController::class, 'update'])->name('product.update');
            Route::delete('/delete/{id}', [ProductController::class, 'destroy'])->name('product.delete');
        });

        // Category Routes
        Route::prefix('categories')->group(function () {
            Route::get('/list', [CategoryController::class, 'index'])->name('category.list');
            Route::get('/create', [CategoryController::class, 'create'])->name('category.create');
            Route::post('/store', [CategoryController::class, 'store'])->name('category.store');
            Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
            Route::post('/update/{id}', [CategoryController::class, 'update'])->name('category.update');
            Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('category.delete');
        });

        // SubCategory Routes
        Route::prefix('subcategories')->group(function () {
            Route::get('/list', [SubCategoryController::class, 'index'])->name('subcategory.list');
            Route::get('/create', [SubCategoryController::class, 'create'])->name('subcategory.create');
            Route::post('/store', [SubCategoryController::class, 'store'])->name('subcategory.store');
            Route::get('/edit/{id}', [SubCategoryController::class, 'edit'])->name('subcategory.edit');
            Route::post('/update/{id}', [SubCategoryController::class, 'update'])->name('subcategory.update');
            Route::delete('/delete/{id}', [SubCategoryController::class, 'destroy'])->name('subcategory.delete');
        });

        // Attribute Routes
        Route::prefix('attributes')->group(function () {
            Route::get('/list', [AttributeController::class, 'index'])->name('attribute.list');
            Route::get('/create', [AttributeController::class, 'create'])->name('attribute.create');
            Route::post('/store', [AttributeController::class, 'store'])->name('attribute.store');
            Route::get('/edit/{id}', [AttributeController::class, 'edit'])->name('attribute.edit');
            Route::post('/update/{id}', [AttributeController::class, 'update'])->name('attribute.update');
            Route::delete('/delete/{id}', [AttributeController::class, 'destroy'])->name('attribute.delete');
        });

        // Brand Routes
        Route::prefix('brands')->group(function () {
            Route::get('/list', [BrandController::class, 'index'])->name('brand.list');
            Route::get('/create', [BrandController::class, 'create'])->name('brand.create');
            Route::post('/store', [BrandController::class, 'store'])->name('brand.store');
            Route::get('/edit/{id}', [BrandController::class, 'edit'])->name('brand.edit');
            Route::post('/update/{id}', [BrandController::class, 'update'])->name('brand.update');
            Route::delete('/delete/{id}', [BrandController::class, 'destroy'])->name('brand.delete');
        });

        // Sale Log Routes
        Route::prefix('sale_log')->group(function () {
            Route::get('/list', [SaleLogController::class, 'index'])->name('sale_log.list');
            Route::get('/create', [SaleLogController::class, 'create'])->name('sale_log.create');
            Route::post('/store', [SaleLogController::class, 'store'])->name('sale_log.store');
            Route::get('/edit/{id}', [SaleLogController::class, 'edit'])->name('sale_log.edit');
            Route::post('/update/{id}', [SaleLogController::class, 'update'])->name('sale_log.update');
            Route::delete('/delete/{id}', [SaleLogController::class, 'destroy'])->name('sale_log.delete');
        });
    });

    // Purchase Management
    Route::prefix('purchase-management')->group(function () {
        // Supplier Routes
        Route::prefix('suppliers')->group(function () {
            Route::get('/list', [SupplierController::class, 'index'])->name('supplier.list');
            Route::get('/create', [SupplierController::class, 'create'])->name('supplier.create');
            Route::post('/store', [SupplierController::class, 'store'])->name('supplier.store');
            Route::get('/edit/{supplier}', [SupplierController::class, 'edit'])->name('supplier.edit');
            Route::post('/update/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
            Route::delete('/delete/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.delete');
        });


        // Purchase Routes
        Route::prefix('purchases')->group(function () {
            Route::get('/show/{id}', [PurchaseController::class, 'show'])->name('purchase.show');
            Route::get('/list', [PurchaseController::class, 'index'])->name('purchase.list');
            Route::get('/create', [PurchaseController::class, 'create'])->name('purchase.create');
            Route::post('/store', [PurchaseController::class, 'store'])->name('purchase.store');
            Route::get('/edit/{purchase}', [PurchaseController::class, 'edit'])->name('purchase.edit');
            Route::post('/update/{purchase}', [PurchaseController::class, 'update'])->name('purchase.update');
            Route::delete('/delete/{purchase}', [PurchaseController::class, 'destroy'])->name('purchase.delete');
        });


    });

    // Shipping Management
    Route::prefix('shipping-management')->group(function () {
        // Shipping Rule Routes
        Route::prefix('shipping-rules')->group(function () {
            Route::get('/list', [ShippingRuleController::class, 'index'])->name('shipping-rule.index');
            Route::get('/create', [ShippingRuleController::class, 'create'])->name('shipping-rule.create');
            Route::post('/store', [ShippingRuleController::class, 'store'])->name('shipping-rule.store');
            Route::get('/edit/{id}', [ShippingRuleController::class, 'edit'])->name('shipping-rule.edit');
            Route::post('/update/{id}', [ShippingRuleController::class, 'update'])->name('shipping-rule.update');
            Route::delete('/delete/{id}', [ShippingRuleController::class, 'destroy'])->name('shipping-rule.destroy');
        });
    });

    // Order Management
    Route::prefix('order-management')->group(function () {
        // Order Routes
        Route::prefix('orders')->group(function () {
            Route::get('/list', [OrderController::class, 'index'])->name('order.list');
            Route::get('/pending_list', [OrderController::class, 'pending_list'])->name('order.pending');
            Route::get('/confirmed_list', [OrderController::class, 'confirmed_list'])->name('order.confirmed');
            Route::get('/rejected_list', [OrderController::class, 'rejected_list'])->name('order.rejected');
            Route::get('/processing_list', [OrderController::class, 'processing_list'])->name('order.processing');
            Route::get('/shipped_list', [OrderController::class, 'shipped_list'])->name('order.shipped');
            Route::get('/completed_list', [OrderController::class, 'completed_list'])->name('order.completed');
            Route::get('/print_invoice/{id}', [OrderController::class, 'print_invoice'])->name('order.print_invoice');

            Route::get('/show', [OrderController::class, 'show'])->name('order.show');
            // Route::post('/store', [OrderController::class, 'store'])->name('order.store');
            Route::get('/edit/{id}', [OrderController::class, 'edit'])->name('order.edit');
            Route::post('/update', [OrderController::class, 'update_status'])->name('order.update.status');
            Route::delete('/delete/{id}', [OrderController::class, 'destroy'])->name('order.delete');
        });
    });

    // Settings Management
    Route::prefix('settings')->group(function () {
        Route::prefix('division')->group(function () {
            Route::get('/list', [DivisionController::class, 'index'])->name('division.index');
            Route::get('/create', [DivisionController::class, 'create'])->name('division.create');
            Route::post('/store', [DivisionController::class, 'store'])->name('division.store');
            Route::get('/edit/{id}', [DivisionController::class, 'edit'])->name('division.edit');
            Route::post('/update/{id}', [DivisionController::class, 'update'])->name('division.update');
            Route::delete('/delete/{id}', [DivisionController::class, 'destroy'])->name('division.destroy');
        });

        Route::prefix('district')->group(function () {
            Route::get('/list', [DistrictController::class, 'index'])->name('district.index');
            Route::get('/create', [DistrictController::class, 'create'])->name('district.create');
            Route::post('/store', [DistrictController::class, 'store'])->name('district.store');
            Route::get('/edit/{id}', [DistrictController::class, 'edit'])->name('district.edit');
            Route::post('/update/{id}', [DistrictController::class, 'update'])->name('district.update');
            Route::delete('/delete/{id}', [DistrictController::class, 'destroy'])->name('district.destroy');
        });

        Route::prefix('police-station')->group(function () {
            Route::get('/list', [PoliceStationController::class, 'index'])->name('police-station.index');
            Route::get('/create', [PoliceStationController::class, 'create'])->name('police-station.create');
            Route::post('/store', [PoliceStationController::class, 'store'])->name('police-station.store');
            Route::get('/edit/{id}', [PoliceStationController::class, 'edit'])->name('police-station.edit');
            Route::post('/update/{id}', [PoliceStationController::class, 'update'])->name('police-station.update');
            Route::delete('/delete/{id}', [PoliceStationController::class, 'destroy'])->name('police-station.destroy');
        });

        Route::prefix('post-office')->group(function () {
            Route::get('/list', [PostOfficeController::class, 'index'])->name('post-office.index');
            Route::get('/create', [PostOfficeController::class, 'create'])->name('post-office.create');
            Route::post('/store', [PostOfficeController::class, 'store'])->name('post-office.store');
            Route::get('/edit/{id}', [PostOfficeController::class, 'edit'])->name('post-office.edit');
            Route::post('/update/{id}', [PostOfficeController::class, 'update'])->name('post-office.update');
            Route::delete('/delete/{id}', [PostOfficeController::class, 'destroy'])->name('post-office.destroy');
        });

        Route::get('/general-settings', [GeneralSettingController::class, 'edit'])->name('admin.general-settings.edit');
        Route::post('/general-settings', [GeneralSettingController::class, 'update'])->name('admin.general-settings.update');
    });




    // AJAX calls
    Route::get('/getProducts', [ProductController::class, 'getProducts'])->name('getProducts');
    Route::get('/get-districts/{division_id}', [DistrictController::class,'getDistricts'])->name('getDistricts');
    Route::get('/get-districts/{division_id}', [DistrictController::class,'getDistricts'])->name('getDistricts');
    Route::get('/get-police-stations/{district_id}', [DistrictController::class, 'getStations'])->name('getStations');
});


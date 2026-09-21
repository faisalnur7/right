<?php

namespace App\Constants;

class NotificationMessages
{
    public const MESSAGES = [

        // PrimeCheckoutController
        'order_placed' => [
            'title' => 'Order #:order_id placed',
            'message' => 'Your order #:order_id has been successfully placed. Total amount: :amount.',
            'route' => 'user.orders.show',
        ],

        'order_placed_admin' => [
            'title' => 'Order #:order_id placed',
            'message' => 'Order #:order_id has been successfully placed. Total amount: :amount.',
            'route' => 'admin.orders.show',
        ],

        // OrderController/update_status
        'order_status_change' => [
            'title' => 'Order #:order_id is :status',
            'message' => 'Your order #:order_id has been :status at :time. Thanks',
            'route' => 'user.orders.show',
        ],

        // KycController/store
        'affiliate_request' => [
            'title' => 'New User :name has joined on your reference.',
            'message' => ':name has joined us on your reference. Thanks for your activity.',
            'route' => 'general_affiliates',
        ],

        // PrimeRequestController/store
        'prime_affiliate_request' => [
            'title' => 'Reference User :username has sent request to become Prime member.',
            'message' => 'Reference User :username - :phone has sent request to become Prime member.',
            'route' => 'prime_requests',
        ],

        'prime_affiliate_request_cancelled' => [
            'title' => 'Reference User :username has rejected your request.',
            'message' => 'Reference User :username - :phone has rejected your request.',
            'route' => null,
        ],

        'prime_affiliate_request_approved' => [
            'title' => 'Reference User :username has approved your request.',
            'message' => 'Reference User :username - :phone has approved your request.',
            'route' => 'kyc.prime',
        ],

        'prime_affiliate_request_admin' => [
            'title' => 'New User :name & Affiliate ID :affiliate_id has sent Prime request.',
            'message' => 'New User :name & Affiliate ID :affiliate_id has sent Prime request.',
            'route' => 'pending_list',
        ],

        // KycController/finish_payment
        'reference_notification_prime_user_payment_request' => [
            'title' => 'New User :name & Affiliate ID :affiliate_id has made a payment.',
            'message' => 'New User :name & Affiliate ID :affiliate_id has made a payment.',
            'route' => 'general_affiliates',
        ],
        
        // KycController/finish_payment
        'admin_notification_prime_user_payment_approve_request' => [
            'title' => 'New User :name & Affiliate ID :affiliate_id has made a payment.',
            'message' => 'New User :name & Affiliate ID :affiliate_id has made a payment.',
            'route' => 'admin.payments.index',
        ],

        'prime_user_payment_approved' => [
            'title' => 'Approved',
            'message' => 'Your membership request is approved by admin. You can enjoy all the facilities of a Prime.',
            'route' => 'user.dashboard',
        ],

        'wallet_notification' => [
            'title' => 'Wallet credited - :type',
            'message' => 'Congratulations! You got :amount taka as :type',
            'route' => 'prime_transactions',
        ],

        'wallet_disbursement_notification' => [
            'title' => 'Wallet debited - :type',
            'message' => 'Congratulations! Withdrawal of :amount taka as :type',
            'route' => 'prime_transactions',
        ],

        // ProductController/store
        'product_added' => [
            'title' => 'New Product Added!!!',
            'message' => 'Good News! New product :product_name added',
            'route' => 'products',
        ],
        
        // app/Services/BinaryTreeService.php
        'sale_log_completed' => [
            'title' => 'Sale log :saleLog completed, :name!',
            'message' => 'Dear :name, your sale log :saleLog is completed. Please be active on the sale log by using any product in your stock. Thanks',
            'route' => 'user.sales.index',
        ],
    ];
}

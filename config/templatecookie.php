<?php

/*
 * This file is part of the Laravel Rave package.
 *
 * (c) templatecookie.com - Zakir Hossen <templatecookie@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    'default_language' => env('APP_DEFAULT_LANGUAGE'),
    'timezone' => env('APP_TIMEZONE'),
    'currency' => env('APP_CURRENCY', 'USD'),
    'currency_symbol' => env('APP_CURRENCY_SYMBOL', '$'),
    'currency_symbol_position' => env('APP_CURRENCY_SYMBOL_POSITION', 'left'),

    'set_ip_based_location' => false,

    'paypal_sandbox_client_id' => 'AQf5e_YjNNqTlpbn6Xtpw_PfIeQTcTnOSZF72ZpRz8xfh1vp5aMtfRuqJq-3DkE2TFWt873wGRPYBOTb',
    'paypal_sandbox_secret' => 'EPvipNvQnf03CRjnv_gljFFSIXq8RMPBcszao0JarXIiKGa0ekPkXwGHJDV6SyJ89crJSA8EnVZKHzxU',
    'paypal_live_client_id' => 'AT3P4kyT_5kQ7cekb4z4JRl9wwjhYJpDEYP_8_AX9zZ3h16h-l1pe5n5rv6WdODNOk_n4Kbn6WHz9eRB',
    'paypal_live_secret' => 'EHTOE0nCzLn_DtiNESl6onVjhpTh44BoGyCkafDDnhFVorb5XXRO8QQLBxHq8GCKRZzxCW2EP-2cleSJ',
    'paypal_mode' => 'live',
    'paypal_active' => true,

    'stripe_key' => '',
    'stripe_secret' => '',
    'stripe_active' => false,

    'razorpay_key' => '',
    'razorpay_secret' => '',
    'razorpay_active' => false,

    'paystack_key' => '',
    'paystack_secret' => '',
    'paystack_payment_url' => "https://api.paystack.co",
    'paystack_merchant' => '',
    'paystack_active' => false,

    'ssl_id' => 'zakir60ce16f5e7c1b',
    'ssl_pass' => 'zakir60ce16f5e7c1b@ssl',
    'ssl_active' => true,
    'ssl_mode' => 'sandbox',

    'flw_public_key' => '',
    'flw_secret' => '',
    'flw_secret_hash' => '',
    'flw_active' => false,

    'im_key' => '',
    'im_secret' => '',
    'im_url' => 'https://test.instamojo.com/api/1.1/',
    'im_active' => false,

    'midtrans_merchat_id' => '',
    'midtrans_client_key' => '',
    'midtrans_server_key' => '',
    'midtrans_active' => false,
    'midtrans_live_mode' => false,

    'mollie_key' => '',
    'mollie_active' => true,

    'google_analytics' => '',
    'google_analytics_status' => false,

    'indeed_id' => '7778623931867371',
    'indeed_limit' => '10',

    'careerjet_id' => '',
    'careerjet_default_locale' => 'en_US',
    'careerjet_limit' => '10',

    'default_job_provider' => 'indeed',

    'pusher_app_id' => env('PUSHER_APP_ID'),
    'pusher_app_key' => env('PUSHER_APP_KEY'),
    'pusher_app_secret' => env('PUSHER_APP_SECRET'),
    'pusher_host' => env('PUSHER_HOST'),
    'pusher_port' => env('PUSHER_PORT'),
    'pusher_schema' => env('PUSHER_SCHEME', 'https'),
    'pusher_app_cluster' => env('PUSHER_APP_CLUSTER'),
    'map_show' => true,
];

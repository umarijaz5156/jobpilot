<?php

/*
 * This file is part of the Laravel Rave package.
 *
 * (c) Oluwole Adebiyi - Flamez <flamekeed@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /**
     * Public Key: Your Rave publicKey. Sign up on https://dashboard.flutterwave.com/ to get one from your settings page
     */
    'publicKey' => config('templatecookie.flw_public_key'),

    /**
     * Secret Key: Your Rave secretKey. Sign up on https://dashboard.flutterwave.com/ to get one from your settings page
     */
    'secretKey' => config('templatecookie.flw_secret'),

    /**
     * Prefix: Secret hash for webhook
     */
    'secretHash' => config('templatecookie.flw_secret_hash'),
];

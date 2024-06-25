<?php

return [
    'mercant_id' => config('templatecookie.midtrans_merchat_id'),
    'client_key' => config('templatecookie.midtrans_client_key'),
    'server_key' => config('templatecookie.midtrans_server_key'),

    'is_production' => config('templatecookie.midtrans_live_mode', false),
    'is_sanitized' => true,
    'is_3ds' => true,
];

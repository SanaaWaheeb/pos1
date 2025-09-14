<?php

// config for alalm3i/EdfaPay
return [
    'merchant_key' => env('EDFA_PAY_MERCHANT_KEY', null),
    'merchant_password' => env('EDFA_PAY_PASSWORD', null),
    'return_url' => env('EDFA_PAY_RETURN_URL', null),

    'notification_class' => \alalm3i\EdfaPay\EdfaPayNotifications::class,

    'notification_path' => '/edfapay/notification',
];

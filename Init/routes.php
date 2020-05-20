<?php

namespace Okay\Modules\OkayCMS\UnitPay;

return [
    'OkayCMS.UnitPay.Callback' => [
        'slug' => 'payment/OkayCMS/UnitPay/callback',
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\CallbackController',
            'method' => 'payOrder',
        ],
    ],
];
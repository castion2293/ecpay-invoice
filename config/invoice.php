<?php

return [
    // 服務位置
    'invoice_url' => env('INVOICE_URL', 'https://einvoice-stage.ecpay.com.tw/B2CInvoice/'),
    // 特店編號
    'merchant_id' => env('INVOICE_MERCHANT_ID', '2000132'),
    // HashKey
    'hash_key' => env('INVOICE_HASH_KEY', 'ejCk326UnaZWKisg'),
    // HashIV
    'hash_iv' => env('INVOICE_HASH_IV', 'q9jcZX8Ib9LM8wYk'),
    // 串接規格文件版號
    'vision' => env('INVOICE_VISION', '3.0.0')
];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PayPal Mode
    |--------------------------------------------------------------------------
    | sandbox — for development and testing
    | live — for real payments
    */
    'mode' => env('PAYPAL_MODE', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | PayPal API Credentials
    |--------------------------------------------------------------------------
    | Get these from https://developer.paypal.com
    */
    'client_id'     => env('PAYPAL_CLIENT_ID', ''),
    'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | KES to USD Exchange Rate
    |--------------------------------------------------------------------------
    | Used to convert contribution amounts from KES to USD for PayPal.
    | Update this regularly or integrate a live exchange rate API.
    */
    'exchange_rate' => env('PAYPAL_EXCHANGE_RATE', 130),

];
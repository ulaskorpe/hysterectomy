<?php

return [

    'app_name' => env('APP_NAME'),
    'app_url' => env('APP_URL'),
    'google_cid' => env('GOOGLE_CID'),
    'google_api_key' => env('GOOGLE_API_KEY'),
    'google_api_key_be' => env('GOOGLE_API_KEY_BE'),
    'app_conversion_media_format' => env('APP_CONVERSION_MEDIA_FORMAT','webp'),
    'app_media_resize_rule' => env('APP_MEDIA_RESIZE_RULE','skip'), //kucuk gorseller gerektiginde resize icin skip, padding eklemek icin padding
    'app_ecommerce_active' => env('APP_ECOMMERCE_ACTIVE',true),
    'app_order_email' => env('APP_ORDER_EMAIL'),
    'app_product_tax_include' => env('APP_PRODUCT_TAX_INCLUDE',false),
    'iyzico_url' => env('IYZICO_URL'),
    'iyzico_key' => env('IYZICO_KEY'),
    'iyzico_secret' => env('IYZICO_SECRET'),
    'parasut_base_url' => env('PARASUT_BASE_URL'),
    'parasut_redirect_url' => env('PARASUT_REDIRECT_URL'),
    'parasut_versiyon' => env('PARASUT_VERSION'),
    'parasut_client_id' => env('PARASUT_CLIENT_ID'),
    'parasut_client_secret' => env('PARASUT_CLIENT_SECRET'),
    'parasut_username' => env('PARASUT_USERNAME'),
    'parasut_password' => env('PARASUT_PASSWORD'),
    'parasut_company_id' => env('PARASUT_COMPANY_ID'),
    'app_appointment_email' => env('APP_APPOINTMENT_EMAIL'),
    'app_appointment_pay_link' => env('APP_APPOINTMENT_PAY_LINK'),

];
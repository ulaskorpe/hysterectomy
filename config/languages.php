<?php

return [

    'active' => explode(',',env('APP_ACTIVE_LANGUAGES','tr,en')),
    'default' => env('APP_LOCALE','tr'),
    'language_bar' => env('APP_LOCALE_SWITCHER',false)

];
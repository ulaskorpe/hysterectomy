<?php

return [

    'option_types' => [
        [
            'label' => 'select',
            'value' => 'select'
        ],
        [
            'label' => 'multiselect',
            'value' => 'multiselect'
        ],
        [
            'label' => 'textarea',
            'value' => 'textarea'
        ],
        [
            'label' => 'input',
            'value' => 'input'
        ],
        [
            'label' => 'number',
            'value' => 'number'
        ],
        [
            'label' => 'date',
            'value' => 'date'
        ],
        [
            'label' => 'datetime',
            'value' => 'datetime'
        ],
        [
            'label' => 'time',
            'value' => 'time'
        ],
        [
            'label' => 'image',
            'value' => 'image'
        ],
        [
            'label' => 'file',
            'value' => 'file'
        ],
        [
            'label' => 'youtube',
            'value' => 'youtube'
        ],
        [
            'label' => 'vimeo',
            'value' => 'vimeo'
        ],
        [
            'label' => 'html5-video',
            'value' => 'html5-video'
        ],
        [
            'label' => 'richtext',
            'value' => 'richtext'
        ],
        [
            'label' => 'selectCountries',
            'value' => 'selectCountries'
        ],
        [
            'label' => 'selectStates',
            'value' => 'selectStates'
        ],
        [
            'label' => 'selectCities',
            'value' => 'selectCities'
        ],
        [
            'label' => 'starCount',
            'value' => 'starCount'
        ],
        [
            'label' => 'reviewSites',
            'value' => 'reviewSites'
        ],
    ],

    'review_sites' => [
        [
            'label' => 'Google Reviews',
            'value' => 'googleReviews'
        ],
        [
            'label' => 'Trust Pilot',
            'value' => 'trustPilot'
        ]
    ],

    'option_site_views' => [
        [
            'label' => 'Default',
            'value' => ''
        ],
        [
            'label' => 'Radio Button',
            'value' => 'radio_button'
        ],
        [
            'label' => 'Dropdown',
            'value' => 'select'
        ],
        [
            'label' => 'Color',
            'value' => 'color'
        ],
        [
            'label' => 'Image',
            'value' => 'image'
        ],
    ],

    'after_order_type' => [
        [
            'label' => 'Dosya indir',
            'value' => 'download'
        ],
        [
            'label' => 'Email Gönder',
            'value' => 'email'
        ],
        [
            'label' => 'Kargo',
            'value' => 'shipping'
        ]
    ],

    'variant_select_types' => [
        [
            'label' => 'default',
            'value' => 'default'
        ],
        [
            'label' => 'radio',
            'value' => 'radio'
        ],
        [
            'label' => 'step-by-step',
            'value' => 'step_by_step'
        ]
    ],

    'menu_locations' => [
        'main',
        'main-wholesale',
        'mobile-main',
        'mobile-main-wholesale',
        'topbar-left',
        'topbar-right',
        'footer-1',
        'footer-2',
        'footer-3',
        'footer-4',
        'footer-5',
        'footer-6',
        'footer-7',
        'footer-8',
        'offcanvas',
        'copyright',
        'before-footer',
        'after-footer',
        'product_categories'
    ],

    'order_statuses' => [
        [
            'label' => 'Yeni Sipariş',
            'value' => 'new'
        ],
        [
            'label' => 'Ödeme Bekleniyor',
            'value' => 'payment_wait'
        ],
        [
            'label' => 'İşleme Alındı',
            'value' => 'in_progress'
        ],
        [
            'label' => 'Kargoya Verildi',
            'value' => 'cargo'
        ],
        [
            'label' => 'Tamamlandı',
            'value' => 'completed'
        ],
        [
            'label' => 'İptal Edildi',
            'value' => 'canceled'
        ],
    ],

    'payment_types' => [
        [
            'label' => 'Kredi Kartı (Iyzico)',
            'value' => 'iyzico',
            'isActive' => true
        ],
        [
            'label' => 'Havale / EFT',
            'value' => 'transfer',
            'isActive' => true
        ]
    ],

    'email_types' => [
        [
            'label' => 'Yeni Üyelik',
            'value' => 'register',
        ],
        [
            'label' => 'Yeni Sipariş',
            'value' => 'order_new',
        ],
        [
            'label' => 'Sipariş Güncellendi',
            'value' => 'order_updated',
        ],
        [
            'label' => 'Admin - Yeni Sipariş',
            'value' => 'admin_order_new',
        ],
        [
            'label' => 'Email gönderilen ürün siparişleri',
            'value' => 'order_type_email',
        ],
        [
            'label' => 'Dosya indirme bazlı ürün siparişleri',
            'value' => 'order_type_download',
        ]
    ],

];

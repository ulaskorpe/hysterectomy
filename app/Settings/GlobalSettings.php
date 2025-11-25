<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GlobalSettings extends Settings
{
    public string $site_name;
    public bool $site_active;
    public ?array $logo;
    public ?string $home_page;
    public ?string $shop_page;
    public ?string $cookie_policy_page;
    public ?string $kvkk_page;
    public ?string $membership_page;
    public ?string $privacy_page;
    public ?string $sale_contract_page;
    public ?string $refund_page;
    public ?string $payment_delivery_page;
    public bool $iyzico_active;
    public bool $parasut_active;
    public ?array $scripts;
    public ?array $contact;
    public ?array $socials;
    public ?array $header_buttons;
    public ?array $footer_slogan;
    public ?array $footer_layout;
    public ?array $footer_form;
    public ?string $blog_content_type;
    public ?string $news_content_type;
    public ?string $faq_content_type;

    public static function group(): string
    {
        return 'global';
    }
}
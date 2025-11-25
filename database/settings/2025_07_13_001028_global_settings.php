<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('global.iyzico_active', false);
        $this->migrator->add('global.parasut_active', false);
        $this->migrator->add('global.sale_contract_page', null);
        $this->migrator->add('global.refund_page', null);
        $this->migrator->add('global.payment_delivery_page', null);
    }
};

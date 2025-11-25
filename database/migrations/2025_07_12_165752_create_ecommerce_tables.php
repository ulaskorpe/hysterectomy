<?php

use App\Models\OrderStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_types', function (Blueprint $table) {
            $table->engine = "InnoDB";
            
            $table->bigIncrements('id');
            $table->uuid();
            $table->string('name');
            $table->string('language',3)->default(config('languages.default'));
            $table->mediumText('summary')->nullable();
            $table->string('slug');
            $table->string('slug_org')->nullable();
            $table->boolean('taxable')->default(true);
            $table->unsignedSmallInteger('tax_rate')->nullable();
            $table->json('additional')->nullable();
            $table->boolean('stock_usage')->default(false);
            $table->boolean('is_published')->default(false);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_cartable')->default(false);
            $table->boolean('is_contactable')->default(false);
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->unsignedBigInteger('product_option_group_id')->nullable();

            $order_types = [];

            if( config('enums.after_order_type') && count(config('enums.after_order_type')) > 0 ){
                foreach (config('enums.after_order_type') as $key => $value) {
                    $order_types[] = $value['value'];
                }
                $table->enum('after_order_type',$order_types);
            } else {
                $table->string('after_order_type',30);
            }

            $table->boolean('popup_products')->default(false);
            $table->unsignedBigInteger('contact_form_id')->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->json('pivot_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
        Schema::create('products', function (Blueprint $table) {
            $table->engine = "InnoDB";

            $table->bigIncrements('id');

            $table->uuid('uuid');
            $table->string('sku')->unique();
            $table->unsignedInteger('product_type_id');
            $table->string('language',3);
            $table->string('name');
            $table->mediumText('summary')->nullable();
            $table->text('description')->nullable();
            $table->json('content')->nullable();
            $table->string('slug');
            $table->string('slug_org')->nullable();
            $table->json('additional')->nullable();
            $table->boolean('is_published')->default(false);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->bigInteger('stock')->nullable();
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->json('attributes')->nullable();
            $table->boolean('use_option_group')->default(false);
            $table->boolean('use_variables')->default(false);
            $table->boolean('featured')->default(false);
            $table->boolean('has_discount')->default(false);
            $table->string('after_order_download_name')->nullable();
            $table->string('after_order_download_file')->nullable();
            $table->string('after_order_email_subject')->nullable();
            $table->text('after_order_email_body')->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->unsignedBigInteger('order_count')->default(0);
            $table->boolean('users_only')->default(false);
            $table->json('pivot_data')->nullable();
            $table->json('seo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('priceable_id'); //product_variants tablosundan veya products tablosundan 
            $table->string('currency_id');
            $table->string('priceable_type'); // variant model mi product model mi
            $table->decimal('price');
            $table->decimal('final_price');
            $table->timestamps();
        });

        Schema::create('product_option_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('language',3)->default(config('languages.default'));
            $table->uuid();
            $table->string('name');
            $table->string('slug');
            $table->string('slug_org')->nullable();
            $table->boolean('has_own_price')->default(true);
            $table->boolean('stock_usage')->default(false);

            $variant_select_types = [];

            if( config('enums.variant_select_types') && count(config('enums.variant_select_types')) > 0 ){
                foreach (config('enums.variant_select_types') as $key => $value) {
                    $variant_select_types[] = $value['value'];
                }
                $table->enum('variant_select_type',$variant_select_types);
            } else {
                $table->string('variant_select_type',30);
            }
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_option_group_options', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('language',3)->default(config('languages.default'));
            $table->uuid();
            $table->string('name');
            $table->string('slug');
            $table->string('slug_org')->nullable();

            $table->foreignId('product_option_group_id')
                ->references('id')
                ->on('product_option_groups')
                ->onDelete('cascade');

            $option_types = [];

            if( config('enums.option_types') && count(config('enums.option_types')) > 0 ){
                foreach (config('enums.option_types') as $key => $value) {
                    $option_types[] = $value['value'];
                }
                $table->enum('option_type',$option_types);
            } else {
                $table->string('option_type',30);
            }
            
            $table->string('site_view',30)->nullable();
            $table->boolean('fe_visible')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('order_column')->nullable()->index();
        });

        Schema::create('product_option_group_option_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('language',3)->default(config('languages.default'));
            $table->uuid();
            $table->foreignId('product_option_group_option_id');
            $table->string('name');
            $table->string('slug');
            $table->string('slug_org')->nullable();
            $table->string('color_value')->nullable();
            $table->string('image_uri')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('order_column')->nullable()->index();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('product_option_group_id');
            $table->json('option_values');
            $table->json('additional')->nullable();
            $table->bigInteger('stock')->nullable();
            $table->boolean('has_discount')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('order_column')->nullable()->index();
        });
        

        Schema::create('product_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('product_type_id');
            $table->uuid('uuid');
            $table->string('language',3);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->mediumText('summary')->nullable();
            $table->text('description')->nullable();
            $table->json('content')->nullable();
            $table->string('slug');
            $table->string('slug_org')->nullable();
            $table->json('additional')->nullable();
            $table->boolean('is_published')->default(false);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('users_only')->default(false);
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->json('pivot_data')->nullable();
            $table->json('seo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_has_categories', function (Blueprint $table) {

            $table->unsignedBigInteger('product_category_id');
            $table->unsignedBigInteger('product_id');

            $table->foreign('product_category_id')
                ->references('id')
                ->on('product_categories')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->primary(['product_category_id','product_id'],'product_has_categories_product_category_id_product_id_primary');

        });

        Schema::create('product_price_discounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('product_price_id')->references('id')
            ->on('product_prices')
            ->onDelete('cascade');
            $table->unsignedBigInteger('user_group_id')->nullable();
            $table->string('discount_type');
            $table->unsignedDecimal('value');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->timestamps();
        });

        Schema::create('product_attributes', function (Blueprint $table) {
            
            $table->bigIncrements('id');
            $table->string('language',3)->default(config('languages.default'));
            $table->uuid();
            $table->string('name');
            $table->string('slug');
            $table->string('slug_org')->nullable();

            $option_types = [];

            if( config('enums.option_types') && count(config('enums.option_types')) > 0 ){
                foreach (config('enums.option_types') as $key => $value) {
                    $option_types[] = $value['value'];
                }
                $table->enum('option_type',$option_types);
            } else {
                $table->string('option_type',30);
            }

            $table->boolean('fe_visible')->default(true);
            $table->boolean('is_required')->default(false);
            $table->string('site_view',30)->nullable();
            $table->json('pivot_data')->nullable();
            $table->boolean('unit_scale')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('order_column')->nullable()->index();

        });

        Schema::create('product_type_has_attributes', function (Blueprint $table) {
            
            $table->unsignedBigInteger('product_attribute_id');
            $table->unsignedBigInteger('product_type_id');

            $table->foreign('product_attribute_id')
                ->references('id')
                ->on('product_attributes')
                ->onDelete('cascade');

            $table->foreign('product_type_id')
                ->references('id')
                ->on('product_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->primary(['product_attribute_id','product_type_id'],'product_type_has_attributes_product_attribute_id_product_type_id_primary');
            
        });

        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('language',3)->default(config('languages.default'));
            $table->uuid();
            $table->unsignedInteger('product_attribute_id');
            $table->string('name');
            $table->string('slug');
            $table->string('slug_org')->nullable();
            $table->string('color_value')->nullable();
            $table->string('image_uri')->nullable();
            $table->json('pivot_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('order_column')->nullable()->index();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('cart_details');
            $table->unsignedSmallInteger('order_status_id');
            $table->decimal('subtotal');
            $table->decimal('total');
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->tinyText('iyzico_token')->nullable();
            $table->string('parasut_fatura_id')->nullable();
            $table->string('parasut_earsiv_id')->nullable();
            $table->json('product_types')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('count');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->decimal('price');
            $table->decimal('discount')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('for_new')->default(false);
            $table->boolean('for_wait_payment')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        foreach (config('enums.order_statuses') as $key => $value) {
            $orderStatus = OrderStatus::create([
                'name' => $value['label'],
                'for_new' => $value['value'] == 'new' ? true : false,
                'for_wait_payment' => $value['value'] == 'payment_wait' ? true : false,
            ]);
        }

        Schema::create('coupon_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_group_id')->references('id')->on('coupon_groups')->onDelete('cascade');
            $table->string('code');
            $table->enum('type',['percentage','fixed']);
            $table->float('discount');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('usage_count')->default(1);
            $table->unsignedInteger('used_count')->default(0);
            $table->boolean('apply_cart')->default(true);
            $table->boolean('as_voucher')->default(false);
            $table->double('used_amount')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('coupon_product_types', function (Blueprint $table) {
            $table->foreignId('coupon_id')->references('id')
            ->on('coupons')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreignId('product_type_id')->references('id')
            ->on('product_types')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::create('cargos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('fixed')->default(true);
            $table->unsignedBigInteger('fixed_price')->nullable();
            $table->unsignedBigInteger('free_after')->nullable();
            $table->unsignedBigInteger('default_product_desi')->default(3);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('users_only')->default(false);
            $table->boolean('apply_cart')->default(false);
            $table->unsignedInteger('min_cart_amount')->default(0);
            $table->string('type',30)->default('percentage');
            $table->double('discount');
            $table->unsignedInteger('user_usage_count')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->json('seo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('campaign_product_types', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->foreignId('campaign_id')->references('id')
            ->on('campaigns')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->unsignedBigInteger('product_type_id');
        });

        Schema::create('product_customer_attributes', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('slug_org')->nullable();
            $table->string('language',3)->default(config('languages.default'));
            $table->uuid();

            $option_types = [];

            if( config('enums.option_types') && count(config('enums.option_types')) > 0 ){
                foreach (config('enums.option_types') as $key => $value) {
                    $option_types[] = $value['value'];
                }
                $table->enum('option_type',$option_types);
            } else {
                $table->string('option_type',30);
            }

            $table->boolean('fe_visible')->default(true);
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('order_column')->nullable()->index();

            $table->json('pivot_data')->nullable();
            $table->string('site_view',30)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_type_has_customer_attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('product_customer_attribute_id');
            $table->unsignedBigInteger('product_type_id');

            $table->foreign('product_customer_attribute_id','fk_product_customer_attr')
                ->references('id')
                ->on('product_customer_attributes')
                ->onDelete('cascade');

            $table->foreign('product_type_id','fk_product_type')
                ->references('id')
                ->on('product_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->primary(['product_customer_attribute_id','product_type_id'],'pk_product_customer_attr_type');
        });

        Schema::create('product_customer_attribute_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('product_customer_attribute_id');
            $table->string('name');
            $table->string('slug');
            $table->string('slug_org')->nullable();
            $table->string('language',3)->default(config('languages.default'));
            $table->uuid();
            $table->json('pivot_data')->nullable();
            $table->string('color_value')->nullable();
            $table->string('image_uri')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('order_column')->nullable()->index();
        });

        Schema::create('cart_temps', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->json('cart_options');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank');
            $table->string('holder');
            $table->string('iban');
            $table->string('currency',20);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('parasuts', function (Blueprint $table) {
            $table->id();
            $table->morphs('parasutable');
            $table->unsignedBigInteger('parasut_id')->unique();
            $table->json('json_data')->nullable();
            $table->timestamps();
        });

        Schema::create('parasut_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parasut_id');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

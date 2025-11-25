<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_used_campaigns', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            
            $table->foreignId('user_id')->references('id')
            ->on('users')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            
            $table->foreignId('campaign_id')->references('id')
            ->on('campaigns')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->uuid();
            $table->mediumText('summary')->nullable();
            $table->string('slug_org');
            $table->string('language',3)->default('tr');
            $table->string('slug');
            $table->json('additional')->nullable();
            $table->unsignedBigInteger('order_column');
            $table->json('content')->nullable();
            $table->boolean('is_published')->default(false);
            $table->json('pivot_data')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('campaign_id')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecommerce_tables');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaysubTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('paysub.subscriber_table_name'), function (Blueprint $table) {
            $table->timestamp('trial_ends_at')->nullable();
        });

        Schema::create(config('paysub.plan_table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->enum('interval_type', ['daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->integer('interval_count')->default(1);
            $table->text('description');
            $table->unsignedBigInteger('amount');
            $table->string('currency')->default('NGN');
            $table->json('tax_rates')->nullable();
            $table->timestamps();
        });

        Schema::create(config('paysub.subscription_table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subscriber_id');
            $table->string('name');
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->enum('status', ['active', 'inactive', 'past_due', 'unpaid'])->default('active');
            $table->integer('quantity')->nullable();
            $table->timestamp('billing_cycle_anchor')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['subscriber_id', 'plan_id']);
        });

        Schema::create(config('paysub.invoice_table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subscription_id');
            $table->string('description')->nullable();
            $table->json('line_items');
            $table->unsignedBigInteger('total');
            $table->timestamp('due_date');
            $table->enum('status', ['paid', 'unpaid', 'void'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create(config('paysub.payment_table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('paystack_id');
            $table->unsignedBigInteger('authorization_id');
            $table->string('reference');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('amount');
            $table->datetime('paid_at');
            $table->timestamps();
        });

        schema::create(config('paysub.card_table_name'), 
            function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('signature')->unique();
            $table->string('type');
            $table->string('last4');
            $table->integer('exp_month');
            $table->year('exp_year');
            $table->string('bin')->nullable();
            $table->string('bank')->nullable();
            $table->string('account_name')->nullable();
            $table->string('country_code')->nullable();
            $table->timestamps();
        });

        schema::create(config('paysub.auth_table_name'), 
            function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subscriber_id');
            $table->unsignedBigInteger('card_id');
            $table->string('email');
            $table->json('auth');
            $table->string('code');
            $table->boolean('default')->default(false);
            $table->timestamps();

            $table->unique(['subscriber_id', 'card_id'], 'auth_subscriber_card_unique');
        });

        schema::create(config('paysub.subscription_items_table_name'), 
            function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('plan_id');
            $table->integer('quantity')->nullable();
            $table->timestamps();

            $table->unique(['subscription_id', 'plan_id'], 's_itms_pl_unq');
        });

        schema::create(config('paysub.feature_table_name'), 
            function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        schema::create(config('paysub.feature_plan_table_name'), 
            function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('feature_id');
            $table->unsignedBigInteger('value')->default(0);
            $table->timestamps();
        });

        schema::create(config('paysub.usage_table_name'), 
            function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('feature_id');
            $table->unsignedInteger('used');
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();

            $table->unique(['item_id', 'feature_id'], 'subitem_feat_unq');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('paysub.subscriber_table_name'), function (Blueprint $table) {
            $table->dropColumns([
                'trial_ends_at'
            ]);
        });
        Schema::dropIfExists(config('paysub.plan_table_name'));
        Schema::dropIfExists(config('paysub.subscription_table_name'));
        Schema::dropIfExists(config('paysub.payment_table_name'));
        Schema::dropIfExists(config('paysub.invoice_table_name'));
        Schema::dropIfExists(config('paysub.card_table_name'));
        Schema::dropIfExists(config('paysub.auth_table_name'));
        Schema::dropIfExists(config('paysub.subscription_items_table_name'));
        Schema::dropIfExists(config('paysub.feature_table_name'));
        Schema::dropIfExists(config('paysub.feature_plan_table_name'));
        Schema::dropIfExists(config('paysub.usage_table_name'));
    }
}

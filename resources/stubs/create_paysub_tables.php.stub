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
            $table->json('paystack_auth')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
        });

        Schema::create(config('paysub.plan_table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('description');
            $table->unsignedBigInteger('amount');
            $table->string('currency')->default('NGN');
            $table->timestamps();
        });

        Schema::create(config('paysub.subscription_table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subscriber_id');
            $table->unsignedBigInteger('plan_id');
            $table->enum('interval', ['monthly', 'yearly'])->default('monthly');
            $table->enum('status', ['active', 'inactive', 'past_due', 'unpaid'])->default('active');
            $table->integer('quantity')->default(1);
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
            $table->json('tax')->nullable();
            $table->unsignedBigInteger('amount');
            $table->timestamp('due_date');
            $table->enum('status', ['paid', 'unpaid', 'void'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create(config('paysub.payment_table_name'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('paystack_id');
            $table->string('auth_code');
            $table->string('reference');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('amount');
            $table->datetime('paid_at');
            $table->timestamps();
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
                'paystack_auth',
                'trial_ends_at'
            ]);
        });
        Schema::dropIfExists(config('paysub.plan_table_name'));
        Schema::dropIfExists(config('paysub.subscription_table_name'));
        Schema::dropIfExists(config('paysub.payment_table_name'));
        Schema::dropIfExists(config('paysub.invoice_table_name'));
    }
}

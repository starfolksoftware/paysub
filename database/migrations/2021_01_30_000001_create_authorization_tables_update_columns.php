<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorizationTablesUpdateColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('paysub.subscriber_table_name'), function (Blueprint $table) {
            $table->dropColumn('paystack_auth');
        });

        Schema::table(config('paysub.payment_table_name'), function (Blueprint $table) {
            $table->dropColumn('auth_code');
            $table->unsignedBigInteger('authorization_id');
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('paysub.subscriber_table_name'), function (Blueprint $table) {
            $table->string('paystack_auth')->nullable();
        });

        Schema::table(config('paysub.payment_table_name'), function (Blueprint $table) {
            $table->string('auth_code');
            $table->dropColumn('authorization_id');
        });

        Schema::dropIfExists(config('paysub.card_table_name'));
        Schema::dropIfExists(config('paysub.auth_table_name'));
    }
}

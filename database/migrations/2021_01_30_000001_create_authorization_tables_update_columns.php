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

        schema::create(config('paysub.auth_table_name'), 
            function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email');
            $table->string('signature')->unique();
            $table->json('auth');
            $table->timestamps();
        });

        schema::create(config('paysub.auth_table_name').'_'.config('paysub.subscriber_table_name'), 
            function (Blueprint $table) {
            $table->unsignedBigInteger('subscriber_id');
            $table->unsignedBigInteger('authorization_id');
            $table->boolean('default')->default(false);

            $table->unique(['subscriber_id', 'authorization_id']);
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

        Schema::dropIfExists(config('paysub.auth_table_name'));
        Schema::dropIfExists(config('paysub.auth_table_name').'_'.config('paysub.subscriber_table_name'));
    }
}

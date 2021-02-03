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

        Schema::create(config('paysub.payment_table_name'), function (Blueprint $table) {
            $table->dropColumn('auth_code');
            $table->unsignedBigInteger('authorization_id')->change();
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
    }
}

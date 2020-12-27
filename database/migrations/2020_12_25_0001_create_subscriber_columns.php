<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriberColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('subscriber_model_name'), function (Blueprint $table) {
            $table->string('paystack_code')->nullable()->index();
            $table->string('card_brand')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('subscriber_model_name'), function (Blueprint $table) {
            $table->dropColumn([
                'paystack_code',
                'card_brand',
                'card_last_four',
                'trial_ends_at',
            ]);
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatesubscriptionItemsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        schema::create(config('paysub.subscription_items_table_name'), 
            function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('plan_id');
            $table->integer('quantity')->nullable();
            $table->timestamps();

            $table->unique(['subscription_id', 'plan_id'], 's_itms_pl_unq');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(config('paysub.subscription_items_table_name'));
    }
}

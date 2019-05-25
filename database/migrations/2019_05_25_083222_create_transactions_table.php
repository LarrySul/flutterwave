<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('account_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('amount');
            $table->integer('charged_amount')->nullable();
            $table->string('currency');
            $table->string('customer_email');
            $table->string('customer_name');
            $table->integer('customer_id')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('transaction_reference');
            $table->string('rave_reference')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('transactions');
    }
}

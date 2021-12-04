<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('restaurant_table_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedInteger('total_price');
            $table->string('transaction_id')->unique();
            $table->string('payment_method')->nullable();
//            $table->string('order_status')->default('payment_pending');
            $table->enum('order_status', ['payment_pending', 'antri', 'dimasak', 'selesai'])->default('payment_pending');
            $table->string('invoice_url')->nullable();
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
        Schema::dropIfExists('orders');
    }
}

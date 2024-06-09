<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('user_id');
            $table->string('tgl_transaksi');
            $table->enum('status_order',['Process','Done','Delivered'])->default('Process');
            $table->string('payment_code', 121)->nullable();
            $table->string('payment_method', 121);
            $table->string('payment_url', 121)->nullable();
            $table->enum('status_payment',['Pending','Success']);
            $table->string('kg');
            $table->string('hari');
            $table->integer('harga');
            $table->integer('disc')->nullable();
            $table->integer('harga_akhir')->nullable();
            $table->string('tgl');
            $table->string('bulan');
            $table->string('tahun');
            $table->string('tgl_ambil')->nullable();
            $table->timestamps();

        });

        Schema::create('harga_transaksi', function (Blueprint $table) {
            $table->foreignId('transaksi_id');
            $table->foreignId('harga_id');
            $table->primary(['transaksi_id','harga_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksis');
        Schema::dropIfExists('harga_transaksi');
    }
}

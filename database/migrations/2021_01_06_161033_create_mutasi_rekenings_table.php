<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMutasiRekeningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mutasi_rekenings', function (Blueprint $table) {
            $table->id();
            $table->string('rekening',30);
            $table->integer('kode_transaksi')->nullable();
            $table->dateTime('tgl_transaksi')->nullable();
            $table->dateTime('tgl_efektif')->nullable();
            $table->dateTime('tgl_efektif_dc')->nullable();
            $table->double('debit',8,2)->nullable();
            $table->double('kredit',8,2)->nullable();
            $table->double('saldo',8,2)->nullable();
            $table->text('description');
            $table->text('copy_row');
            $table->integer('insert_user');
            $table->tinyInteger('show')->default('1');
            $table->string('compare_row',125);
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
        Schema::dropIfExists('mutasi_rekenings');
    }
}

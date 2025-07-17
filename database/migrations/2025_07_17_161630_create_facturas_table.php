<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_registro');
            $table->date('fecha_factura');
            $table->unsignedBigInteger('solicitud_id');
            $table->string('factura');
            $table->string('proveedor');
            $table->text('concepto_gasto');
            $table->string('situacion');
            $table->decimal('importe', 12, 2);
            $table->string('objeto_gasto');
            $table->string('c_c');
            $table->softDeletes();
            $table->timestamps();
        
            $table->foreign('solicitud_id')->references('id')->on('solicitudes')->onDelete('cascade');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};

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
        Schema::table('solicitudes', function (Blueprint $table) {
            // Cambiar el tipo de string a unsignedBigInteger
            $table->unsignedBigInteger('area')->nullable()->change();

            $table->foreign('area')
                  ->references('id')
                  ->on('areas')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            // Eliminar la relación primero
            $table->dropForeign(['area']);

            // Regresar el campo a string si deseas revertir
            $table->string('area')->change();
        });
    }
};

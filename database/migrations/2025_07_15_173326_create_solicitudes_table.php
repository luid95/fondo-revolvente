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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->date('fecha');
            $table->string('area');
            $table->string('personas');
            $table->string('uso');
            $table->decimal('monto', 10, 2);
            $table->decimal('saldo_restante', 10, 2)->default(0);
            $table->string('estado')->default('en proceso'); // en proceso | completado | eliminado
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};

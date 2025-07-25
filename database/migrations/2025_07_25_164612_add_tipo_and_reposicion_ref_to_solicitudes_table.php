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
            $table->string('tipo')->default('normal'); // 'normal' o 'reposicion'
            $table->foreignId('reposicion_generada_id')->nullable()->constrained('reposiciones')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropColumn('tipo');
            $table->dropForeign(['reposicion_generada_id']);
            $table->dropColumn('reposicion_generada_id');
        });
    }
};

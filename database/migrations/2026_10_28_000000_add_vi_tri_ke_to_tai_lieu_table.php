<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tailieu', function (Blueprint $table) {
            $table->string('viTriKe', 100)->nullable()->after('maIsbn');
        });
    }

    public function down(): void
    {
        Schema::table('tailieu', function (Blueprint $table) {
            $table->dropColumn('viTriKe');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lawyers', function (Blueprint $table) {
            $table->decimal('consultation_fee', 10, 2)->default(75.00)->after('total_reviews');
            $table->decimal('legal_advice_fee', 10, 2)->default(35.00)->after('consultation_fee');
        });
    }

    public function down(): void
    {
        Schema::table('lawyers', function (Blueprint $table) {
            $table->dropColumn(['consultation_fee', 'legal_advice_fee']);
        });
    }
};

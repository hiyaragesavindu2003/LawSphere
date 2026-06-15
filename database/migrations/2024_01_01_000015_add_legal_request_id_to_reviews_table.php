<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('legal_request_id')->nullable()->after('appointment_id')
                ->constrained()->nullOnDelete();
            $table->unique(['client_id', 'legal_request_id']);
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['legal_request_id']);
            $table->dropUnique(['client_id', 'legal_request_id']);
            $table->dropColumn('legal_request_id');
        });
    }
};

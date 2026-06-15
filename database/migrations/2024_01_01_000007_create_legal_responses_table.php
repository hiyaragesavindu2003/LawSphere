<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lawyer_id')->constrained()->cascadeOnDelete();
            $table->text('response_text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_responses');
    }
};

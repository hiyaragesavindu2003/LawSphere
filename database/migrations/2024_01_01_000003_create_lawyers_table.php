<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lawyers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('qualifications')->nullable();
            $table->string('specialization');
            $table->unsignedInteger('experience_years')->default(0);
            $table->text('biography')->nullable();
            $table->string('bar_number', 100)->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->timestamps();

            $table->index('specialization');
            $table->index('is_approved');
            $table->index('average_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lawyers');
    }
};

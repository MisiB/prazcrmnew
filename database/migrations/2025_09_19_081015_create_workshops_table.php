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
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('target');
            $table->string('location');
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->integer('limit');
            $table->decimal('Cost', 10, 2);
            $table->string('Status');
            $table->char('created_by', 36);
            $table->string('document_url')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshops');
    }
};

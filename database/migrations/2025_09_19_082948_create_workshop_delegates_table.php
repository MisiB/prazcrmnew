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
        Schema::create('workshop_delegates', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('workshopinvoice_id')->nullable('workshop_invoices')->constrained()->cascadeOnDelete();
            $table->foreignId('workshop_id')->constrained('workshops')->cascadeOnDelete();
            $table->foreignId('workshoporder_id')->nullable('workshoporders')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('surname');
            $table->string('email');
            $table->string('phone');
            $table->string('designation');
            $table->string('national_id');
            $table->string('title');
            $table->string('gender');
            $table->string('type')->nullable();
            $table->string('company')->nullable();
            $table->timestamps();

            // Foreign key constraints
            // $table->foreign('workshopinvoice_id')->references('id')->on('workshopinvoices')->onDelete('cascade');
            // $table->foreign('workshop_id')->references('id')->on('workshops')->onDelete('cascade');
            // $table->foreign('workshoporder_id')->references('id')->on('workshoporders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_delegates');
    }
};

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
        Schema::create('workshoporders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workshop_id');
            $table->string('name');
            $table->string('surname');
            $table->string('email');
            $table->string('phone');
            $table->integer('delegates');
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->foreignId('exchangerate_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('ordernumber');
            $table->string('invoicenumber')->nullable();
            $table->string('documenturl')->nullable();
            $table->string('status');
            $table->timestamps();

            // Foreign key constraints
            // $table->foreign('workshop_id')->references('id')->on('workshops')->onDelete('cascade');
            // $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            // $table->foreign('exchangerate_id')->references('id')->on('exchangerates')->onDelete('set null');
            // $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshoporders');
    }
};

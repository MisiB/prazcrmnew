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
        Schema::create('workshop_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('surname');
            $table->string('email');
            $table->string('organisation');
            $table->string('invoicenumber');
            $table->integer('delegates');
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->decimal('cost', 10, 2);
            $table->string('status');
            $table->string('account_type');
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Foreign key constraints
            // $table->foreign('workshop_id')->references('id')->on('workshops')->onDelete('cascade');
            // $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            // $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_invoices');
    }
};

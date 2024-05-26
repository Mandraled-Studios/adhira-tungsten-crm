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
        Schema::disableForeignKeyConstraints();

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->date('payment_date');
            $table->boolean('paid_in_full');
            $table->decimal('amount_paid', 8, 2);
            $table->decimal('balance', 8, 2);
            $table->string('payment_method');
            $table->boolean('refunded')->default(false);
            $table->foreignId('invoice_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};

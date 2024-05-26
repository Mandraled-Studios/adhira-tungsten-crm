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

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->date('invoice_date');
            $table->date('duedate');
            $table->decimal('subtotal', 8, 2);
            $table->decimal('tax1', 8, 2)->default(0);
            $table->decimal('tax2', 8, 2)->default(0);
            $table->decimal('total', 8, 2);
            $table->string('tax1_label', 8)->nullable();
            $table->string('tax2_label', 8)->nullable();
            $table->string('invoice_status');
            $table->foreignId('task_id');
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
        Schema::dropIfExists('invoices');
    }
};

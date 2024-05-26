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

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);
            $table->string('assessment_year', 30);
            $table->string('status', 64);
            $table->dateTime('duedate');
            $table->unsignedBigInteger('assigned_user_id');
            $table->string('frequency_override', 128)->nullable();
            $table->boolean('billing_status')->default(true);
            $table->decimal('billing_value', 8, 2)->nullable()->default(0);
            $table->string('billing_company');
            $table->foreignId('task_type_id');
            $table->foreignId('client_id');
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
        Schema::dropIfExists('tasks');
    }
};

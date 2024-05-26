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

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('assigned_user_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('assigned_user_id')->after('billing_company')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['assigned_user_id']);
        });
    }
};

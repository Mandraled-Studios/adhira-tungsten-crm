<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function __construct() {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    
        Schema::disableForeignKeyConstraints();

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 255);
            $table->string('firm_type', 255);
            $table->string('pan_number', 15);
            $table->string('client_code', 32)->nullable();
            $table->string('client_name', 128)->nullable();
            $table->string('aadhar_number', 12)->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('whatsapp', 15)->nullable();
            $table->string('email', 234)->nullable();
            $table->string('alternate_email', 254)->nullable();
            $table->string('website', 128)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('state', 64)->nullable();
            $table->string('country', 64)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('tan_no', 20)->nullable();
            $table->string('cin_no', 20)->nullable();
            $table->string('gstin', 16)->nullable();
            $table->foreignId('auditor_group_id')->constrained('users');
            $table->string('billing_at', 128)->nullable()->default('Adhira Associates');
            $table->boolean('client_status')->default(true);
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
        Schema::dropIfExists('clients');
    }
};

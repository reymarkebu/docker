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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->enum('account_type', ['prospect', 'lead', 'account'])->default('prospect');
            $table->text('address')->nullable();
            $table->string('industry')->nullable();
            $table->string('email_address1')->unique();
            $table->string('email_address2')->nullable();
            $table->string('email_address3')->nullable();
            $table->string('phone_number1')->nullable();
            $table->string('phone_number2')->nullable();
            $table->string('phone_number3')->nullable();
            $table->integer('company_size')->nullable();
            $table->string('contact_person')->nullable();
            $table->tinyInteger('island_group')->default(1);
            $table->tinyInteger('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

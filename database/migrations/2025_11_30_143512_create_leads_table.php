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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies');
            $table->string('designation')->nullable();
            $table->tinyInteger('email_status')->default(1);
            $table->timestamp('email_status_at')->nullable();
            $table->tinyInteger('proposal_type')->default(1);
            $table->timestamp('proposal_type_at')->nullable();
            $table->tinyInteger('phone_call_status')->default(1);
            $table->timestamp('phone_called_at')->nullable();
            $table->enum('has_existing_hmo', ['yes', 'no'])->default('no');
            $table->date('expiry_date')->nullable();
            $table->tinyInteger('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

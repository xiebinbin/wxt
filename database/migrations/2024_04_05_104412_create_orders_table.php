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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id')->index('agent_id_index');
            $table->unsignedBigInteger('product_id')->index('product_id_index');
            $table->string('name');
            $table->string('id_card');
            $table->string('phone')->index('phone_index');
            $table->string('address');
            $table->enum('status', ['PASSED', 'PENDING', 'REJECTED'])->default('PENDING')->index('status_index');
            $table->string('reject_reason')->nullable();
            $table->string('logistics_company')->nullable();
            $table->string('logistics_number')->nullable();
            $table->timestamp('passed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->enum('settlement_status', ['FAILED', 'SUCCESS', 'PENDING'])->default('PENDING')->index('settlement_status_index');
            $table->string('settlement_failed_reason')->nullable();
            $table->bigInteger('settlement_amount')->default(0);
            $table->bigInteger('commission_amount')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

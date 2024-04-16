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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('remark')->nullable();
            $table->bigInteger('balance')->default(0)->comment('余额 单位：分');
            $table->string('qrcode')->nullable();
            $table->integer('order_count')->default(0);
            $table->integer('valid_order_count')->default(0);
            $table->bigInteger('total_income')->default(0)->comment('总收入');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};

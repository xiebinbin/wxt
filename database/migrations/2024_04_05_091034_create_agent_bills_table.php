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
        Schema::create('agent_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id')->comment('代理商ID')->index('agent_id_index');
            $table->integer('amount')->comment('账单金额 单位分');
            $table->enum('type', ['INCOME', 'EXPENSE'])->comment('收入或支出')->default('INCOME');
            $table->string('remark')->nullable()->comment('备注');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_bills');
    }
};

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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('list_cover')->nullable();
            $table->string('cover')->nullable();
            $table->string('title')->nullable('标题');
            $table->string('subtitle')->nullable('副标题');
            $table->text('description')->nullable('描述');
            $table->integer('monthly_rent')->nullable('月租');
            $table->longText('monthly_rent_description')->nullable()->comment('月租描述');
            $table->integer('traffic')->nullable('流量');
            $table->longText('traffic_description')->nullable()->comment('流量描述');
            $table->longText('call_description')->nullable()->comment('通话描述');
            $table->longText('discount_description')->nullable()->comment('优惠信息');
            $table->longText('rent_introduction')->nullable()->comment('资费介绍');
            $table->longText('reminder')->nullable('温馨提示');
            $table->dateTime('expired_at')->nullable();
            $table->string('badge')->nullable();
            $table->json('tags')->default('[]')->comment('标签');
            $table->integer('apply_count')->default(0)->comment('办理人数');
            $table->integer('commission')->default(0)->comment('佣金');
            $table->integer('order_count')->default(0)->comment('订单数');
            $table->integer('valid_order_count')->default(0)->comment('有效订单');
            $table->integer('settlement_order_count')->default(0)->comment('结算订单数');
            $table->integer('settlement_commission_amount')->default(0)->comment('结算佣金');
            $table->tinyInteger('status')->default(1)->comment('状态 1-正常 2-停用');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

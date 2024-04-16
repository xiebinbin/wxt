<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderListRequest;
use App\Http\Requests\OrderSubmitRequest;
use App\Services\AgentService;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function store(OrderSubmitRequest $request)
    {
        $data = $request->all();
        // 查询处代理
        $product = ProductService::findByCode($data['product_code']);
        $agent = AgentService::findByCode($data['code']);
        if (empty($product) || empty($agent)) {
            return response()->json(['code' => 400, 'message' => '下单失败'], 400);
        }
        $data['agent_id'] = $agent['id'];
        $data['product_id'] = $product['id'];
        $data['address'] = $data['address'] . $data['address_detail'];
        DB::beginTransaction();
        try {
            OrderService::create($data);
            AgentService::incOrderCount($product['id']);
            DB::commit();
            return response()->json(['code' => 200, 'message' => '下单成功'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['code' => 400, 'message' => '下单失败'], 400);
        }
    }
    public function index()
    {
        return Inertia::render('Orders/Index');
    }
    public function getList(OrderListRequest $request){
        $phone = $request->input('phone');
        $orders= OrderService::getList($phone);
        return response([
            'code'=>200,
            'data'=>[
                'orders'=>$orders
            ],
            'msg'=>null
        ]);
    }
}

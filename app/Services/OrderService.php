<?php

namespace App\Services;

use App\Libs\XCache;
use App\Models\Agent;
use App\Models\Order;
use App\Services\Traits\SqidsService;
use App\Services\Traits\UrlService;

class OrderService
{
    use SqidsService;
    use UrlService;
    protected static string $SQIDS_ALPHABET = '012345fghijklabcd6789emnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected static int $SQIDS_MIN_LENGTH = 6;
    public static function findByCode(string $code)
    {
        $id = self::codeToId($code);
        if (!$id) {
            return [];
        }
        return self::findById($id);
    }
    public static function findById(int $id)
    {
        return XCache::remember("orders:{$id}", 3600, function () use ($id) {
            $item = Order::find($id);
            return $item ? $item->toArray() : [];
        });
    }
    public static function clearCache(int $id)
    {
        return XCache::forget("orders:{$id}");
    }
    public static function create(array $data) : Order {
        $order = new Order();
        $order->agent_id = $data['agent_id'];
        $order->product_id = $data['product_id'];
        $order->name = $data['name'];
        $order->id_card = $data['id_card'];
        $order->phone = $data['phone'];
        $order->address = $data['address'];
        $order->status = 'PENDING';
        $order->settlement_status = 'PENDING';
        $order->save();
        return $order;
    }
    public static function getList(string $phone){
        $orders = Order::query()->where('phone',$phone)->offset(0)->limit(100)->latest('created_at')->get();
        return $orders->map(function(Order $order): array{
            return [
                'code'=>self::idToCode($order->id),
                'product_cover'=>self::url($order->product->list_cover),
                'product_name'=>$order->product->subtitle,
                'product_code'=>ProductService::idToCode($order->product->id),
                'name'=>preg_replace('/(?<=.)./u', '*', $order->name),
                'id_card'=>preg_replace('/(\d{5})\d{1,}([\d|X]{4})/', '$1****$2',$order->id_card),
                'address'=>$order->address,
                'phone'=>preg_replace('/(\d{3})\d{4}(\d{4})/', '$1****$2',$order->phone),
                'status'=>$order->status,
                'logistics_company'=>$order->logistics_company,
                'logistics_number'=>$order->logistics_number,
                'reject_reason'=>$order->reject_reason,
                'created_at'=>$order->created_at,
                'passed_at'=>$order->passed_at,
            ];
        });
    }
}

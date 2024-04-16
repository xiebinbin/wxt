<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function show(string $productCode)
    {
        $item = ProductService::findByCode($productCode);
        if (!empty($item)) {
            $item = [
                'code' => ProductService::idToCode($item['id']),
                'title' => $item['title'],
                'cover' => ProductService::url($item['cover']),
                'expired_at' => $item['expired_at'],
                'badge' => $item['badge'],
                'apply_count' => $item['apply_count'],
                'reminder' => $item['reminder'],
                'description' => $item['description'],
                'monthly_rent' => $item['monthly_rent'],
                'monthly_rent_description' => $item['monthly_rent_description'],
                'traffic' => $item['traffic'],
                'traffic_description' => $item['traffic_description'],
                'call_description' => $item['call_description'],
                'discount_description' => $item['discount_description'],
                'rent_introduction' => $item['rent_introduction'],
            ];
        }
        return Inertia::render('Product/Index', [
            'product' => $item
        ]);
    }
}

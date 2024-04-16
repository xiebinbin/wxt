<?php

namespace App\Services;

use App\Libs\XCache;
use App\Models\Product;
use App\Services\Traits\SqidsService;
use App\Services\Traits\UrlService;

class ProductService
{
    use SqidsService;
    use UrlService;
    public static array $TAGS = ['推荐', '大流量', '超便宜', '长期套餐', '中国移动', '中国联通', '中国电信', '中国广电'];
    protected static string $SQIDS_ALPHABET = 'abcdefghijklmnoxyzABCDEFGHpqrstuvwIJKLMNOPQRSTUVWXYZ0123456789';
    protected static int $SQIDS_MIN_LENGTH = 6;
    public static function search(string $keyword)
    {
        $search = Product::search($keyword)->where('status', 1)->orderBy('created_at', 'DESC')->raw();
        $ids = collect($search['hits'])->map(fn ($item) => $item['id'])->all();
        $items = [];
        foreach ($ids as $id) {
            $item = self::findById($id);
            $items[] = [
                'code' => self::idToCode($item['id']),
                'title' => $item['title'],
                'description' => $item['description'],
                'badge' => $item['badge'],
                'apply_count' => $item['apply_count'],
                'list_cover' => self::url($item['list_cover']),
                'expired_at' => $item['expired_at'],
            ];
        }
        return $items;
    }
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
        return XCache::remember("product:{$id}", 3600, function () use ($id) {
            $item = Product::find($id);
            return $item ? $item->toArray() : [];
        });
    }
    // 清除缓存
    public static function clearCache(int $id)
    {
        return XCache::forget("product:{$id}");
    }
    public static function incApplyCount(int $id)
    {
        return Product::where('id', $id)->increment('order_count');
    }
}

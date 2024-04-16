<?php
namespace App\Libs;

use Illuminate\Support\Facades\Cache;

class XCache extends Cache{
    public static function remember($key, $ttl, $callback)
    {
        if (self::has($key)) {
            return self::get($key);
        }
        // 防止缓存击穿
        self::put($key, null, $ttl);
        $item = $callback();
        self::put($key, $item, $ttl);
        return $item;
    }
}
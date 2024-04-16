<?php

namespace App\Services;

use App\Libs\XCache;
use App\Models\Agent;
use App\Services\Traits\SqidsService;
use App\Services\Traits\UrlService;

class AgentService
{
    use SqidsService;
    use UrlService;
    protected static string $SQIDS_ALPHABET = '0123456789fghijklabcdemnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected static int $SQIDS_MIN_LENGTH = 4;
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
        return XCache::remember("agent:{$id}", 3600, function () use ($id) {
            $item = Agent::find($id);
            $item['qrcode'] = self::url($item['qrcode']);
            return $item ? $item->toArray() : [];
        });
    }
    public static function clearCache(int $id)
    {
        return XCache::forget("agent:{$id}");
    }
    public static function incOrderCount(int $id)
    {
        return Agent::where('id', $id)->increment('order_count');
    }
}

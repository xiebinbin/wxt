<?php

namespace App\Services;

use App\Libs\XCache;
use App\Models\Banner;
use App\Services\Traits\UrlService;

class BannerService
{
    use UrlService;
    public static function getOnlineBanners()
    {
        return XCache::remember('online:banners', 3600, function () {
            return Banner::query()->where('status', 'ON')->orderBy('sort', 'DESC')->get(['img', 'url'])->map(function ($item) {
                return [
                    'img' => ProductService::url($item->img),
                    'url' => $item->url
                ];
            });
        });
    }
}

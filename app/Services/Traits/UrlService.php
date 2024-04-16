<?php

namespace App\Services\Traits;

use Illuminate\Support\Facades\Storage;
use Sqids\Sqids;

trait UrlService
{
    public static function url($path){
        if(strpos($path, 'http') === 0){
            return $path;
        }
        $url = Storage::disk('bitiful')->url($path);
        // 检测是否为图片
        if (preg_match('/.*(\\.png|\\.jpg|\\.jpeg|\\.gif)$/', $url)) {
            $url .= '?fmt=webp';
        }
        return $url;
    }
}

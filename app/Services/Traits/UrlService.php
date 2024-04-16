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
        return Storage::disk('bitiful')->url($path);
    }
}

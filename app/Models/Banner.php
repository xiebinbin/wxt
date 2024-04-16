<?php

namespace App\Models;

use App\Libs\XCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'img',
        'url',
        'sort',
        'status',
        'title'
    ];
    // 监听更新与创建事件
    public static function boot(){
        
        parent::boot();
        self::creating(function(){
            XCache::forget('online:banners');
        });
        self::updating(function(){
            XCache::forget('online:banners');
        });
        self::deleting(function(){
            XCache::forget('online:banners'); 
        });
    }
}

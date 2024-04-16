<?php

namespace App\Models;

use App\Services\AgentService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'qrcode',
        'remark'
    ];
    use HasFactory;
    use SoftDeletes;
    public static function boot()
    {
        parent::boot();
        static::updated(
            function ($model) {
                // 删除缓存
                AgentService::clearCache($model->id);
            }
        );
    }
}

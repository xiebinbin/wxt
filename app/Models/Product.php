<?php

namespace App\Models;

use App\Services\ProductService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\EngineManager;
use Laravel\Scout\Engines\Engine;
use Laravel\Scout\Searchable;

class Product extends Model
{
    protected $casts = [
        'tags' => 'array',
    ];
    protected $fillable = [
        'title',
        'subtitle',
        'list_cover',
        'cover',
        'description',
        'monthly_rent',
        'monthly_rent_description',
        'traffic',
        'traffic_description',
        'call_description',
        'discount_description',
        'rent_introduction',
        'reminder',
        'expired_at',
        'badge',
        'tags',
        'apply_count',
        'commission',
        'order_count',
        'valid_order_count',
        'settlement_order_count',
        'settlement_commission_amount',
        'status'
    ];
    use HasFactory;
    use SoftDeletes;
    use Searchable;
    /**
     * 获取与模型关联的索引的名称.
     */
    public function searchableAs(): string
    {
        return 'products_index';
    }
    /**
     * 获取模型的可索引数据。
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'tags' => $this->tags,
            'created_at' => now()->getTimestamp(),
            'status' => $this->status,
        ];
    }
    /**
     * 获取这个模型用于索引的值.
     */
    public function getScoutKey(): mixed
    {
        return $this->id;
    }

    /**
     * 获取这个模型用于索引的键.
     */
    public function getScoutKeyName(): mixed
    {
        return 'id';
    }
    /**
     * 获取这个模型用于索引的搜索引擎.
     */
    public function searchableUsing(): Engine
    {
        return app(EngineManager::class)->engine('meilisearch');
    }
    // 监听更新
    public static function boot()
    {
        parent::boot();
        static::updated(
            function ($model) {
                // 删除缓存
                ProductService::clearCache($model->id);
            }
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $with = ['agent', 'product'];
    protected $fillable = [
        'agent_id',
        'product_id',
        'name',
        'id_card',
        'phone',
        'address',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

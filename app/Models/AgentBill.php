<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentBill extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $with=[
        'agent'
    ];
    public function agent(){
        return $this->belongsTo(Agent::class);
    }
}

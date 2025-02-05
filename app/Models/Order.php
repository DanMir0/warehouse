<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_status_id', 'counterparty_id', 'finished_at'];

    public function ordersTechCards()
    {
        return $this->hasMany(OrderTechCard::class, 'order_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTechCard extends Model
{
    public $timestamps = false;

    protected $table = 'orders_tech_cards';
    protected $fillable = ['order_id', 'tech_card_id', 'quantity'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechCardProduct extends Model
{
//    use HasFactory;
    protected $table = 'tech_cards_products';
    protected $fillable = ['product_id', 'tech_card_id', 'quantity'];
}

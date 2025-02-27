<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechCard extends Model
{
//    use HasFactory;
    protected $fillable = ['name', 'product_id'];

    public function productTechCards()
    {
        return $this->hasMany(TechCardProduct::class, 'tech_card_id');
    }

    public function products()
    {
        return $this->belongsToMany(Products::class, 'tech_cards_products', 'tech_card_id', 'product_id')
            ->withPivot('quantity'); // если есть поле "количество"
    }
}

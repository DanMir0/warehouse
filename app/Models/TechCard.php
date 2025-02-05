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
}

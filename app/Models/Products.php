<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
//    use HasFactory;

    protected $fillable = ['name', 'unitsId', 'residue'];

    public function unitOfMeasurements()
    {
        return $this->belongsTo(UnitsOfMeasurements::class, 'unitsId');
    }

    public function techCards()
    {
        return $this->belongsToMany(TechCard::class, 'tech_card_products', 'product_id', 'tech_card_id');
    }

}

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
}

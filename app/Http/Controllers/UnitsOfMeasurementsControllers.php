<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\UnitsOfMeasurements;
use Illuminate\Http\Request;

class UnitsOfMeasurementsControllers extends Controller
{
    public function getUnits()
    {
        $unitsOfMeasurements = UnitsOfMeasurements::all();
        return response()->json($unitsOfMeasurements);
    }
}

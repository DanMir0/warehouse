<?php

namespace App\Http\Controllers;

use App\Models\UnitsOfMeasurements;
use Illuminate\Http\Request;

class UnitsOfMeasurementsController extends Controller
{
    public function index()
    {
       return view('index');
    }

    public function getUnits()
    {
        $unitsOfMeasurements = UnitsOfMeasurements::all();
        return response()->json($unitsOfMeasurements);
    }

    public function create()
    {
       return view('index');
    }

    public function edit()
    {
       return view('index');
    }



    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
        ]);

        $unit = UnitsOfMeasurements::find($id);

        if ($unit) {
            $unit->update([
                'name' => $data['name'],
            ]);
        }


       return view('index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required'
        ]);

        $units = UnitsOfMeasurements::create([
            'name' => $validated['name'],
        ]);

        return response()->json($units->id);
    }
    public function getUnit($id)
    {
        $unit = UnitsOfMeasurements::select('id', 'name')->where('id', $id)->first();

        if ($unit) {
            return response()->json($unit);
        }

        return response()->json(['error' => 'Единица измерения не найдена.'], 404);
    }

    public function destroy($id)
    {
        $measuring_unit = UnitsOfMeasurements::find($id);

        if ($measuring_unit) {
            $measuring_unit->delete();
        }
    }
}

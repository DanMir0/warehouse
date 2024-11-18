<?php

namespace App\Http\Controllers;

use App\Models\Counterparties;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CounterpartiesController extends Controller
{
    //
    public function index()
    {
        return view('index');
    }

    public function create()
    {
        return view('index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|unique:counterparties,name',
            'contact_info' => 'required|regex:/^\+7[0-9]{10}$/',
            'address' => 'required|string|min:5',
            'inn' => 'required|numeric|digits:10',
            'contact_persons' => 'required|string|regex:/^[^\d]+$/',
        ]);

        $counterparty = new Counterparties([
            'name' => $validated['name'],
            'contact_info' => $validated['contact_info'],
            'address' => $validated['address'],
            'inn' => $validated['inn'],
            'contact_persons' => $validated['contact_persons'],
        ]);

        $counterparty->save();
    }

    public function getCounterparties()
    {
        $counterparties = Counterparties::all();
        return response()->json($counterparties);
    }
    public function getCounterparty($id)
    {
        $counterparties = Counterparties::find($id);
        return response()->json($counterparties);
    }

    public function edit()
    {
        return view('index');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', Rule::unique('counterparties', 'name')->ignore($id)],
            'contact_info' => ['required', 'regex:/^\+7[0-9]{10}$/'],
            'address' => ['required', 'string', 'min:5'],
            'inn' => ['required', 'numeric', 'digits:10'],
            'contact_persons' => ['required', 'string', 'regex:/^[^\d]+$/'],
        ]);

        $counterparty = Counterparties::find($id);

        if ($counterparty) {
            $counterparty->update([
                'name' => $data['name'],
                'contact_info' => $data['contact_info'],
                'address' => $data['address'],
                'inn' => $data['inn'],
                'contact_persons' => $data['contact_persons'],
            ]);
        }

        return view('index');
    }

    public function destroy($id)
    {
        $counterparty = Counterparties::find($id);

        if ($counterparty) {
            $counterparty->delete();
        }
    }
}

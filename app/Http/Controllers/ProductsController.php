<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use function Laravel\Prompts\select;

class ProductsController extends Controller
{
    public function index()
    {
        return view('products.index');
    }

    public function getProducts()
    {
        $products = Products::select('p.id', 'p.name', 'p.residue', 'um.name as measuring_unit_name',)
            ->from('products as p')->join('units_of_measurements as um', 'p.unitsId', '=', 'um.id')->get();
        return response()->json($products);
    }

    public function create()
    {
        return view('products.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'measuring_unit_id' => 'required'
        ]);

        $product = new Products([
            'name' => $validated['name'],
            'unitsId' => $validated['measuring_unit_id'],
            'residue' => 0,
        ]);

        $product->save();
    }

    public function destroy($id)
    {
        $product = Products::find($id);

        if ($product) {
            $product->delete();
        }
    }

    public function edit()
    {
        return view('products.index');
    }

    public function getProduct($id)
    {
        $product = Products::select('p.id', 'p.name', 'p.residue', 'um.id as measuring_unit_id' )
            ->from('products as p')
            ->join('units_of_measurements as um', 'p.unitsId', '=', 'um.id')
            ->where('p.id', $id)
            ->first();

        if ($product) return response()->json($product);

        return response()->json(['message' => 'Товар не найден'], 404);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'measuring_unit_id' => ['required', 'integer'],
        ]);

        $product = Products::find($id);

        if ($product) {
            $product->update([
                'name' => $data['name'],
                'unitsId' => $data['measuring_unit_id'],
            ]);
        }

        return view('products.index');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\TechCardProduct;
use Illuminate\Http\Request;

class TechCardProductController extends Controller
{
    public function getTechCardProducts($tech_card_id)
    {
        $products = TechCardProduct::select('tcp.quantity', 'tcp.product_id as product_id', 'p.name as product_name', 'um.name as measuring_unit_name')
            ->from('tech_cards_products as tcp')
            ->join('products as p', 'tcp.product_id', '=', 'p.id')
            ->join('units_of_measurements as um', 'p.unitsId', '=', 'um.id')
            ->join('tech_cards as tc', 'tcp.tech_card_id', '=', 'tc.id')
            ->where('tcp.tech_card_id', $tech_card_id)->get();

        if ($products) return response()->json($products);

        return response()->json(['message' => 'Материалы не найдены'], 404);
    }
}

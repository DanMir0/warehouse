<?php

namespace App\Http\Controllers;

use App\Models\DocumentProduct;
use Illuminate\Http\Request;

class DocumentProductController extends Controller
{
   public function getProducts($document_id)
   {
        $products = DocumentProduct::select('dp.product_id', 'dp.quantity', 'p.name as product_name', 'um.name as measuring_unit_name')
            ->from('documents_products as dp')
            ->join('products as p', 'p.id', '=', 'dp.product_id')
            ->join('units_of_measurements as um', 'um.id', '=', 'p.unitsId' )
            ->where('dp.document_id',$document_id)
            ->get();

       if ($products) return response()->json($products);

       return response()->json(['message' => 'Материалы не найдены'], 404);
   }
}

<?php

namespace App\Http\Controllers;

use App\Models\DocumentProduct;
use Illuminate\Http\Request;

class DocumentProductController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_id' => 'required|numeric',
            'product_id' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);

        $document_product = new DocumentProduct([
            'document_id' => $validated['document_id'],
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
        ]);

        $document_product->save();
    }
}

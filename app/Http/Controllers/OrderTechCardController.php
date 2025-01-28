<?php

namespace App\Http\Controllers;

use App\Models\OrderTechCard;
use Illuminate\Http\Request;

class OrderTechCardController extends Controller
{
    public function getOrderTechCard($order_id)
    {
        $products = OrderTechCard::select('otc.quantity', 'otc.tech_card_id', 'p.id as product_id', 'p.name as product_name')
            ->from('orders_tech_cards as otc')
            ->join('orders as o', 'otc.order_id', '=', 'o.id')
            ->join('tech_cards as tc', 'otc.tech_card_id', '=', 'tc.id')
            ->join('products as p', 'tc.product_id', '=', 'p.id')
            ->where('otc.order_id', $order_id)
            ->get();

        if ($products) return response()->json($products);

        return response()->json(['message' => 'Материалы не найдены'], 404);
    }
}

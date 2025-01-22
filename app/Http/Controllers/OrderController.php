<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderTechCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function create()
    {
        return view('index');
    }

    public function edit()
    {
        return view('index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_status_id' => 'required|integer',
            'counterparty_id' => 'required|integer|exists:counterparties,id',
            'finished_at' => 'nullable',
            'products' => 'required|array',
            'products.*.tech_card_id' => 'required|integer|exists:tech_cards,id',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::create([
                'order_status_id' => $validated['order_status_id'],
                'counterparty_id' => $validated['counterparty_id'],
                'finished_at' => $validated['finished_at'],
            ]);

            foreach ($validated['products'] as $product) {
                OrderTechCard::create([
                    'order_id' => $order->id,
                    'tech_card_id' => $product['tech_card_id'],
                    'quantity' => $product['quantity']
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Заказ успешно сохранена!'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ошибка при сохранении.', 'error' => $e->getMessage()], 201);
        }
    }

    public function getOrders()
    {
        $orders = Order::select('o.*', 'os.name as order_status_name', 'c.name as counterparty_name')
            ->from('orders as o')
            ->join('order_statuses as os', 'o.order_status_id', '=', 'os.id')
            ->join('counterparties as c', 'o.counterparty_id', '=', 'c.id')
            ->get();

        return response()->json($orders);
    }
}

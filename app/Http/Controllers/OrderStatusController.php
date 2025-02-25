<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderStatusController extends Controller
{
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
            'name' => 'required|string|unique:order_statuses,name',
        ]);

        $order_status = new OrderStatus([
            'name' => $validated['name'],
        ]);

        $order_status->save();
        return response()->json($order_status);
    }

    public function getOrderStatuses()
    {
        $order_statuses = OrderStatus::all();
        return response()->json($order_statuses);
    }

    public function edit()
    {
        return view('index');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string', Rule::unique('order_statuses', 'name')->ignore($id)],
        ]);

        $order_status = OrderStatus::find($id);

        if ($order_status) {
            $order_status->update([
                'name' => $data['name'],
            ]);
        }

        return view('index');
    }

    public function getOrderStatus($id)
    {
        $order_status = OrderStatus::find($id);
        return response()->json($order_status);
    }

    public function destroy($id)
    {
        $order_status = OrderStatus::find($id);

        if ($order_status) {
            $order_status->delete();
        }
    }
}

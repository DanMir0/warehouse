<?php

namespace App\Http\Controllers;

use App\Helpers\Common\OrderStatuses;
use App\Models\Document;
use App\Models\Order;
use App\Models\OrderTechCard;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DocumentController;

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
            return response()->json($order->id);
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

    public function getOrder($id)
    {
        $order = Order::select('o.*', 'os.id as order_status_id', 'c.id as counterparty_id')
            ->from('orders as o')
            ->join('order_statuses as os', 'o.order_status_id', '=', 'os.id')
            ->join('counterparties as c', 'o.counterparty_id', '=', 'c.id')
            ->where('o.id', $id)
            ->first();

        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'order_status_id' => ['required', 'integer'],
            'counterparty_id' => ['required', 'integer'],
            'products' => ['nullable', 'array'],
            'products.*.order_id' => ['required', 'integer', 'exists:orders,id'],
            'products.*.tech_card_id' => ['required', 'integer', 'exists:tech_cards,id'],
            'products.*.old_tech_card_id' => ['nullable', 'integer', 'exists:tech_cards,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:1'],
            'deletedProducts' => ['nullable', 'array'],
            'deletedProducts.*' => ['integer', 'exists:orders_tech_cards,tech_card_id'],
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);
            $order->update([
                'order_status_id' => $validated['order_status_id'],
                'counterparty_id' => $validated['counterparty_id'],
            ]);
            // Удаление старых продуктов, добавление новых или обновление существующих
            if (!empty($validated['deletedProducts'])) {
                OrderTechCard::where('order_id', $id)
                    ->whereIn('tech_card_id', $validated['deletedProducts'])
                    ->delete();
            }

            if (!empty($validated['products'])) {
                foreach ($validated['products'] as $product) {
                    // Найдем продукт по старому tech_card_id, если он передан
                    if (!empty($product['old_tech_card_id'])) {
                        $existingProduct = OrderTechCard::where('order_id', $id)
                            ->where('tech_card_id', $product['old_tech_card_id'])
                            ->first();

                        if ($existingProduct) {
                            // Если продукт найден, обновляем его
                            $updatedData = [
                                'quantity' => $product['quantity'],
                                'tech_card_id' => $product['tech_card_id'], // Обновляем на новый tech_card_id
                            ];

                            // Обновляем запись в таблице orders_tech_cards
                            $existingProduct->update($updatedData);
                        }
                    } else {

                        // Если old_tech_card_id не передан, проверяем на уникальность перед добавлением
                        $existingProduct = OrderTechCard::where('order_id', $id)
                            ->where('tech_card_id', $product['tech_card_id'])
                            ->first();

                        if (!$existingProduct) {
                            // Если такой записи нет, добавляем новый продукт
                            OrderTechCard::create([
                                'order_id' => $id,
                                'tech_card_id' => $product['tech_card_id'],
                                'quantity' => $product['quantity'],
                            ]);
                        } else {
                            // Если запись существует, обновляем её
                            $existingProduct->update([
                                'quantity' => $product['quantity'],
                            ]);
                        }
                    }
                }
            }
            DB::commit();

            return response()->json(['message' => 'Заказ успешно обновлен!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Ошибка при обновлении.', 'error' => $e->getMessage()], 500);
        }
    }

    public function setStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'order_status_id' => ['required', 'integer', 'exists:order_statuses,id'],
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);

            $order->update([
                'order_status_id' => $validated['order_status_id'],
            ]);

            // Обработка смены статуса и создание документа
            if ($validated['order_status_id'] == OrderStatuses::STATUS_IN_PROGRESS) {
                $response = (new DocumentController)->generateProductionDocument($order);
                if ($response->getStatusCode() != 201) {
                    throw new \Exception($response->getData()->error);
                }
            } elseif ($validated['order_status_id'] == OrderStatuses::STATUS_FINISHED) {
                (new DocumentController)->generateFinishedDocument($order);
            } elseif ($validated['order_status_id'] == OrderStatuses::STATUS_ISSUED) {
                (new DocumentController)->generateIssuedDocument($order);
            }
            DB::commit();

            return response()->json(['message' => 'Статус заказа успешно обновлен', 'order' => $order], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Ошибка при обновлении статуса.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if ($order) {
            $order->ordersTechCards()->delete();

            $order->delete();
        }
    }

    public function getDocument($id)
    {
        $document = Document::select('d.*', 'dt.name as document_type_name')
            ->from('documents as d')
            ->join('document_types as dt', 'dt.id', '=', 'd.document_type_id')
            ->where('d.order_id', $id)
            ->get();

        return response()->json($document);
    }

    public function print($id)
    {
        $order = Order::with([
            'ordersTechCards.product.unitOfMeasurements',
            'counterparty'
        ])->findOrFail($id);

        $organization = DB::table('settings as s')
            ->join('counterparties as c', 's.value', '=', 'c.id')
            ->where('s.key', 'CUSTOMER_ID')
            ->select('c.*')
            ->first();

        $pdf = Pdf::loadView('prints.order', compact('order', 'organization'))->setPaper('a4');

        return $pdf->stream("order_{$id}.pdf");
    }

}

<?php

namespace App\Http\Controllers;

use App\Helpers\Common\DocumentTypes;
use App\Models\Counterparties;
use App\Models\Document;
use App\Models\DocumentProduct;
use App\Models\Order;
use App\Models\OrderTechCard;
use App\Models\TechCardProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
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

    public function getDocuments()
    {
        $documents = Document::select('d.*', 'dt.name as document_type_name', 'c.name as counterparty_name',)
            ->from('documents as d')
            ->join('document_types as dt', 'd.document_type_id', '=', 'dt.id')
            ->join('counterparties as c', 'd.counterparty_id', '=', 'c.id')
            ->get();

        return response()->json($documents);
    }

    public function getDocument($id)
    {
        $document = Document::select('d.*')
            ->from('documents as d')
            ->join('document_types as dt', 'd.document_type_id', '=', 'dt.id')
            ->join('counterparties as c', 'd.counterparty_id', '=', 'c.id')
            ->leftjoin('orders as o', 'd.order_id', '=', 'o.id')
            ->where('d.id', $id)
            ->first();

        return response()->json($document);
    }

    public function generateProductionDocument(Order $order)
    {
        try {
            DB::beginTransaction();

            // Находим контрагент Производственный цех
            $counterparty = Counterparties::where('name', 'Производственный цех')->first();
            if (!$counterparty) {
                throw new \Exception("Контрагент 'Производственный цех' не найден");
            }

            // Создаем документ
            $document = Document::create([
                'document_type_id' => DocumentTypes::OUTCOME,
                'counterparty_id' => $counterparty->id,
                'order_id' => $order->id,
            ]);
            if (!$document) {
                throw new \Exception("Ошибка при создании документа");
            }

            // Находим товары из заказа
            $order_tech_cards = OrderTechCard::where('order_id', $order->id)->get();

            // Находим материалы из чего изготавлвается товар
            foreach ($order_tech_cards as $order_tech_card) {
                $tech_card_products = TechCardProduct::where('tech_card_id', $order_tech_card->tech_card_id)->get();

                foreach ($tech_card_products as $tech_card_product) {
                    $total_quantity = $order_tech_card->quantity * $tech_card_product->quantity;

                    // Если материал уже есть, то прибавляем к этому же товару количество иначе создаем новое поле
                    $existinProduct = DocumentProduct::where('document_id', $document->id)
                        ->where('product_id', $tech_card_product->product_id)
                        ->first();

                    if ($existinProduct) {
                        $existinProduct->update([
                            'quantity' => $existinProduct->quantity + $total_quantity,
                        ]);
                    } else {
                        DocumentProduct::create([
                            'document_id' => $document->id,
                            'product_id' => $tech_card_product->product_id,
                            'quantity' => $total_quantity,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Документ успешно сохранена!'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Ошибка при сохранении.', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type_id' => 'required|integer',
            'counterparty_id' => 'required|integer|exists:counterparties,id',
            'order_id' => 'nullable',
            'products' => 'required|array',
            'products.*.document_id' => 'nullable|integer|exists:documents,id',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            $document = Document::create([
                'document_type_id' => $validated['document_type_id'],
                'counterparty_id' => $validated['counterparty_id'],
                'order_id' => $validated['order_id'] ?? null,
            ]);

            foreach ($validated['products'] as $product) {
                DocumentProduct::create([
                    'document_id' => $document->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity']
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Документ успешно сохранен!'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ошибка при сохранении.', 'error' => $e->getMessage()], 201);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'document_type_id' => 'required|integer',
            'counterparty_id' => 'required|integer|exists:counterparties,id',
            'order_id' => 'nullable',
            'products' => 'required|array',
            'products.*.document_id' => ['nullable', 'integer', 'exists:documents,id'],
            'products.*.product_id' => ['required_with:products', 'integer', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:1'],
            'deletedProducts' => ['nullable', 'array'],
            'deletedProducts.*' => ['integer']
        ]);

        try {
            DB::beginTransaction();

            $document = Document::findOrFail($id);
            $document->update([
                'document_type_id' => $validated['document_type_id'],
                'counterparty_id' => $validated['counterparty_id'],
                'order_id' => $validated['order_id'] ?? null,
            ]);

            if (!empty($validated['deletedProducts'])) {
                DocumentProduct::where('document_id', $id)
                    ->whereIn('product_id', $validated['deletedProducts'])
                    ->delete();
            }

            foreach ($validated['products'] as $product) {
                // Найдем продукт по старому tech_card_id, если он передан
                if (!empty($product['old_product_id'])) {
                    $existingProduct = DocumentProduct::where('document_id', $id)
                        ->where('product_id', $product['old_product_id'])
                        ->first();

                    if ($existingProduct) {
                        // Если продукт найден, обновляем его
                        $updatedData = [
                            'quantity' => $product['quantity'],
                            'product_id' => $product['product_id'], // Обновляем на новый tech_card_id
                        ];

                        // Обновляем запись в таблице orders_tech_cards
                        $existingProduct->update($updatedData);
                    }
                } else {

                    // Если old_tech_card_id не передан, проверяем на уникальность перед добавлением
                    $existingProduct = DocumentProduct::where('document_id', $id)
                        ->where('product_id', $product['product_id'])
                        ->first();

                    if (!$existingProduct) {
                        // Если такой записи нет, добавляем новый продукт
                        DocumentProduct::create([
                            'document_id' => $id,
                            'product_id' => $product['product_id'],
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

            DB::commit();
            return response()->json(['message' => 'Документ успешно обновлен!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Ошибка при обновлении.', 'error' => $e->getMessage()], 500);
        }
    }
}


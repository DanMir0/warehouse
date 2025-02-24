<?php

namespace App\Http\Controllers;

use App\Helpers\Common\DocumentTypes;
use App\Models\Counterparties;
use App\Models\Document;
use App\Models\DocumentProduct;
use App\Models\Order;
use App\Models\OrderTechCard;
use App\Models\Products;
use App\Models\TechCard;
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

            // Находим контрагента "Производственный цех"
            $counterparty = Counterparties::where('name', 'Производственный цех')->first();
            if (!$counterparty) {
                throw new \Exception("Контрагент 'Производственный цех' не найден");
            }

            // Проверяем наличие всех необходимых материалов перед созданием документа
            $order_tech_cards = OrderTechCard::where('order_id', $order->id)->get();
            $missingMaterials = [];  // Массив для хранения информации о нехватке материалов


            foreach ($order_tech_cards as $order_tech_card) {
                $tech_card_products = TechCardProduct::where('tech_card_id', $order_tech_card->tech_card_id)->get();

                foreach ($tech_card_products as $tech_card_product) {
                    $total_quantity = $order_tech_card->quantity * $tech_card_product->quantity;

                    // Проверяем остатки
                    $product = Products::find($tech_card_product->product_id);
                    if (!$product || $product->residue < $total_quantity) {
                        $missingMaterials[] = [
                            'product_id' => $tech_card_product->product_id,
                            'quantity' => $total_quantity - $product->residue, // недостающий объем
                            'product_name' => $product ? $product->name : 'Неизвестный товар'
                        ];
                    }
                }
            }

            // Если есть недостающие материалы, выбрасываем ошибку
            if (!empty($missingMaterials)) {
                throw new \Exception('Недостаточно материалов: ' . json_encode($missingMaterials));
            }

            // Если все материалы есть → создаем документ
            $document = Document::create([
                'document_type_id' => DocumentTypes::OUTCOME,
                'counterparty_id' => $counterparty->id,
                'order_id' => $order->id,
            ]);
            if (!$document) {
                throw new \Exception("Ошибка при создании документа");
            }

            // Теперь списываем материалы
            foreach ($order_tech_cards as $order_tech_card) {
                $tech_card_products = TechCardProduct::where('tech_card_id', $order_tech_card->tech_card_id)->get();

                foreach ($tech_card_products as $tech_card_product) {
                    $total_quantity = $order_tech_card->quantity * $tech_card_product->quantity;

                    // Получаем товар
                    $product = Products::find($tech_card_product->product_id);

                    // Если материал уже есть в документе, обновляем количество, иначе создаем запись
                    $existingProduct = DocumentProduct::where('document_id', $document->id)
                        ->where('product_id', $tech_card_product->product_id)
                        ->first();

                    if ($existingProduct) {
                        $existingProduct->update([
                            'quantity' => $existingProduct->quantity + $total_quantity,
                        ]);
                    } else {
                        DocumentProduct::create([
                            'document_id' => $document->id,
                            'product_id' => $tech_card_product->product_id,
                            'quantity' => $total_quantity,
                        ]);
                    }

                    // Уменьшаем остаток в products
                    $product->decrement('residue', $total_quantity);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Документ успешно создан!'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Ошибка при создании документа.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function generateFinishedDocument(Order $order)
    {
        try {
            DB::beginTransaction();

            // Находим контрагент Производственный цех
            $counterparty = Counterparties::where('name', 'Производственный цех')->first();
            if (!$counterparty) {
                throw new \Exception("Контрагент 'Производственный цех' не найден");
            }

            $document = Document::create([
                'document_type_id' => DocumentTypes::INCOME,
                'counterparty_id' => $counterparty->id,
                'order_id' => $order->id,
            ]);

            if (!$document) {
                throw new \Exception("Ошибка при создании документа");
            }

            $order_tech_cards = OrderTechCard::where('order_id', $order->id)->get();

            foreach ($order_tech_cards as $order_tech_card) {
                $tech_card = TechCard::where('id', $order_tech_card->tech_card_id)->first();

                if ($tech_card) {
                    $product = Products::where('id', $tech_card->product_id)->first();

                    if ($product) {
                        DocumentProduct::create([
                            'document_id' => $document->id,
                            'product_id' => $product->id,
                            'quantity' => $order_tech_card->quantity,
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

    public function generateIssuedDocument(Order $order)
    {
        try {
            DB::beginTransaction();

            $document = Document::create([
                'document_type_id' => DocumentTypes::OUTCOME,
                'counterparty_id' => $order->counterparty_id,
                'order_id' => $order->id,
            ]);

            $order_tech_cards = OrderTechCard::where('order_id', $order->id)->get();

             foreach ($order_tech_cards as $order_tech_card) {
                 $tech_card = TechCard::where('id', $order_tech_card->tech_card_id)->first();

                 if ($tech_card) {
                     $product = Products::where('id', $tech_card->product_id)->first();

                     if ($product) {
                         DocumentProduct::create([
                             'document_id' => $document->id,
                             'product_id' => $product->id,
                             'quantity' => $order_tech_card->quantity,
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

                if ($validated['document_type_id'] == DocumentTypes::INCOME) {
                    $counterparty = Counterparties ::find($validated['counterparty_id']);

                    if ($counterparty && $counterparty->name == 'Производственный цех') {
                        Products::where('id', $product['product_id'])
                            ->increment('residue', $product['quantity']);
                    }
                }
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

    public function destroy($id)
    {
        try {
            $document = Document::findOrFail($id);
            $document->delete();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ошибка при удалении'], 500);
        }
    }
}


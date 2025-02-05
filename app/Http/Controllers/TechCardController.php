<?php

namespace App\Http\Controllers;

use App\Models\TechCard;
use App\Models\TechCardProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TechCardController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function getTechCards()
    {
        $tech_cards = TechCard::select('tc.*', 'p.name as product_name')
            ->from('tech_cards as tc')
            ->join('products as p', 'tc.product_id', '=', 'p.id')
            ->get();

        return response()->json($tech_cards);
    }

    public function create()
    {
        return view('index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:tech_cards,name',
            'product_id' => 'required|integer|exists:products,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Создание техкарты
            $tech_card = TechCard::create([
                'name' => $validated['name'],
                'product_id' => $validated['product_id'],
            ]);

            // Добавление продуктов в техкарту
            foreach ($validated['products'] as $product) {
                TechCardProduct::create([
                    'product_id' => $product['product_id'],
                    'tech_card_id' => $tech_card->id,
                    'quantity' => $product['quantity'],
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Тех карта успешно сохранена!'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ошибка при сохранении.', 'error' => $e->getMessage()], 500);
        }
    }

    public function edit()
    {
        return view('index');
    }

    public function getTechCard($id)
    {
        $tech_card = TechCard::select('tc.*', 'p.id as product_id')
            ->from('tech_cards as tc')
            ->join('products as p', 'tc.product_id', '=', 'p.id')
            ->where('tc.id', $id)
            ->first();

        if ($tech_card) return response()->json($tech_card);

        return response()->json(['message' => 'Тех карта не найдена!'], 404);
    }

    public function destroy($id)
    {
        try {
            $tech_card = TechCard::findOrFail($id);
            if ($tech_card) {
                $tech_card->productTechCards()->delete();

                $tech_card->delete();
            }
        } catch (\Exception $e) {
            if ($this->isForeignKeyConstraintViolation($e)) {
                return response()->json([
                    'message' => 'Невозможно удалить тех карту, так как она используется в заказах. Пожалуйста, удалите записи из заказов перед удалением.'
                ], 500);
            }
        }
    }

    private function isForeignKeyConstraintViolation(\Exception $e): bool
    {
        return str_contains($e->getMessage(), 'FOREIGN KEY constraint failed');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', Rule::unique('tech_cards', 'name')->ignore($id)],
            'product_id' => ['required', 'integer'],
            'products' => ['nullable', 'array'],
            'products.*.product_id' => ['required_with:products', 'integer', 'exists:products,id'],
            'products.*.quantity' => ['required_with:products', 'numeric', 'min:1'],
            'deletedProducts' => ['nullable', 'array'],
            'deletedProducts.*' => ['integer', 'exists:tech_cards_products,product_id'],
        ]);

        try {
            DB::beginTransaction();

            $tech_card = TechCard::findOrFail($id);
            $tech_card->update([
                'name' => $validated['name'],
                'product_id' => $validated['product_id'],
            ]);

            if (!empty($validated['deletedProducts'])) {
                TechCardProduct::where('tech_card_id', $id)
                    ->whereIn('product_id', $validated['deletedProducts'])
                    ->delete();
            }

            if (!empty($validated['products'])) {
                foreach ($validated['products'] as $product) {
                    $existingProduct = TechCardProduct::where('tech_card_id', $id)
                        ->where('product_id', $product['product_id'])
                        ->first();

                    if ($existingProduct) {
                        // Обновляем количество, если связь уже есть
                        $existingProduct->update([
                            'quantity' => $product['quantity'],
                        ]);
                    } else {
                        // Если связи нет, создаем новую запись
                        TechCardProduct::create([
                            'product_id' => $product['product_id'],
                            'tech_card_id' => $id,
                            'quantity' => $product['quantity'],
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json(['message' => 'Тех карта успешно обновлена!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Ошибка при обновлении.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getProductsTechCard()
    {
        $products = TechCard::join('products', 'tech_cards.product_id', '=', 'products.id')
            ->select('products.name', 'products.id', 'tech_cards.id as tech_card_id')
            ->distinct()
            ->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'Нет продуктов в техкартах'], 404);
        }

        return response()->json($products);
    }
}

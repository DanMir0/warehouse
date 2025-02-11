<?php

namespace App\Http\Controllers;

use App\Helpers\Common\DocumentTypes;
use App\Models\Document;
use App\Models\Order;
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
        $document = Document::select('d.*', 'dt.id as document_type_id ', 'c.id as counterparty_id', 'o.id as order_id')
            ->from('documents as d')
            ->join('document_types as dt', 'd.document_type_id', '=', 'dt.id')
            ->join('counterparties as c', 'd.counterparty_id', '=', 'c.id')
            ->join('orders as o', 'o.order_id', '=', 'o.id')
            ->where('d.id', $id)
            ->first();

        return response()->json($document);
    }

    public function generateProductionDocument(Order $order)
    {
        try {
            DB::beginTransaction();

            Document::create([
                'document_type_id' => DocumentTypes::OUTCOME,
                'counterparty_id' => $order['counterparty_id'],
                'order_id' => $order['id'],
            ]);

            DB::commit();
            return response()->json(['message' => 'Документ успешно сохранена!'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Ошибка при сохранении.', 'error' => $e->getMessage()], 201);

        }
    }

}

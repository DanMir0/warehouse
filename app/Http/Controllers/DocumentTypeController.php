<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DocumentTypeController extends Controller
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
            'name' => 'required|string|unique:document_types,name',
            'in_out' => 'required',
        ]);

        $document_type = new DocumentType([
            'name' => $validated['name'],
            'in_out' => $validated['in_out'],
        ]);

        $document_type->save();
    }

    public function getDocumentTypes()
    {
        $document_types = DocumentType::all();
        return response()->json($document_types);
    }

    public function edit()
    {
        return view('index');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string', Rule::unique('document_types', 'name')->ignore($id)],
            'in_out' => ['required', 'string'],
        ]);

        $document_type = DocumentType::find($id);

        if ($document_type) {
            $document_type->update([
                'name' => $data['name'],
                'in_out' => $data['in_out'],
            ]);
        }

        return view('index');
    }

    public function getDocumentType($id)
    {
        $document_type = DocumentType::find($id);
        return response()->json($document_type);
    }

    public function destroy($id)
    {
        $document_type = DocumentType::find($id);

        if ($document_type) {
            $document_type->delete();
        }
    }
}

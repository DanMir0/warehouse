<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|array', // Ожидаем массив
            'value' => 'required|array', // Ожидаем массив
        ]);

        foreach ($validated['key'] as $index => $key) {
            Setting::updateOrInsert(
                ['key' => $key],
                ['value' => $validated['value'][$index]],
            );
        }
        return response()->json([
            'success' => true,
            'message' => 'Настройки сохранены'
        ]);
    }

    public function index()
    {
        return view('index');
    }

    public function getSettings()
    {
        $settings = Setting::all();
        return response()->json($settings);
    }
}

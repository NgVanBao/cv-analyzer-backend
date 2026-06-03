<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AILog;

class AILogController extends Controller
{
    public function index()
    {
        return response()->json(AILog::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaCV' => 'required|integer|exists:HoSoCV,MaCV',
            'ThoiGian' => 'required|date',
            'TrangThai' => 'required|string|max:50',
            'NoiDungLog' => 'required|string',
        ]);
        $aiLog = AILog::create($validated);
        return response()->json([
            'message' => 'Thêm AI Log thành công!',
            'data' => $aiLog
        ], 201);
    }

    public function show($id)
    {
        return response()->json(AILog::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $aiLog = AILog::findOrFail($id);
        $validated = $request->validate([
            'MaCV' => 'required|integer|exists:HoSoCV,MaCV',
            'ThoiGian' => 'required|date',
            'TrangThai' => 'required|string|max:50',
            'NoiDungLog' => 'required|string',
        ]);
        $aiLog->update($validated);
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => $aiLog
        ]);
    }

    public function destroy($id)
    {
        $aiLog = AILog::findOrFail($id);
        $aiLog->delete();
        return response()->json([
            'message' => 'Xóa AI Log thành công!'
        ]);
    }
}

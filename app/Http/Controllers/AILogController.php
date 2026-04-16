<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AILog;

class AILogController extends Controller
{
    public function index()
    {
        $aiLogs = AILog::all();
        return view('ai_log.index', compact('aiLogs'));
    }

    public function create()
    {
        return view('ai_log.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaCV' => 'required|integer|exists:ho_so_cv,MaCV',
            'ThoiGian' => 'required|date',
            'TrangThai' => 'required|string|max:50',
            'NoiDungLog' => 'required|string',
        ]);
        AILog::create($validated);
        return redirect()->route('ai_log.index')->with('success', 'Thêm AI Log thành công!');
    }

    public function edit($id)
    {
        $aiLog = AILog::findOrFail($id);
        return view('ai_log.edit', compact('aiLog'));
    }

    public function update(Request $request, $id)
    {
        $aiLog = AILog::findOrFail($id);
        $validated = $request->validate([
            'MaCV' => 'required|integer|exists:ho_so_cv,MaCV',
            'ThoiGian' => 'required|date',
            'TrangThai' => 'required|string|max:50',
            'NoiDungLog' => 'required|string',
        ]);
        $aiLog->update($validated);
        return redirect()->route('ai_log.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $aiLog = AILog::findOrFail($id);
        $aiLog->delete();
        return redirect()->route('ai_log.index')->with('success', 'Xóa AI Log thành công!');
    }
}

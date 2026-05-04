<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KyNangTrongCV;
class KyNangTrongCVController extends Controller
{
    public function index()
    {
        return response()->json(KyNangTrongCV::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaKyNang' => 'required|integer|exists:TuDienKyNang,MaKyNang',
            'MaCV' => 'required|integer|exists:HoSoCV,MaCV',
            'MucDo' => 'nullable|string|max:50',
        ]);
        $kyNang = KyNangTrongCV::create($validated);
        return response()->json([
            'message' => 'Thêm kỹ năng vào CV thành công!',
            'data' => $kyNang
        ], 201);
    }

    public function show($id)
    {
        return response()->json(KyNangTrongCV::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $kyNangTrongCV = KyNangTrongCV::findOrFail($id);
        $validated = $request->validate([
            'MaKyNang' => 'required|integer|exists:TuDienKyNang,MaKyNang',
            'MaCV' => 'required|integer|exists:HoSoCV,MaCV',
            'MucDo' => 'nullable|string|max:50',
        ]);
        $kyNangTrongCV->update($validated);
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => $kyNangTrongCV
        ]);
    }

    public function destroy($id)
    {
        $kyNangTrongCV = KyNangTrongCV::findOrFail($id);
        $kyNangTrongCV->delete();
        return response()->json([
            'message' => 'Xóa kỹ năng khỏi CV thành công!'
        ]);
    }
}

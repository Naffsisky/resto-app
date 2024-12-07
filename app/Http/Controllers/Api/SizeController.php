<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Size;
use Illuminate\Support\Facades\Validator;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }

            $size = Size::paginate(10);

            if ($size->isNotEmpty()) {
                return response([
                    'message' => 'success',
                    'data' => $size
                ], 200);
            } else {
                return response([
                    'message' => 'error',
                    'data' => 'Tidak ada data ukuran'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|min:3|max:100',
        ], [
            'nama.required' => 'Nama Ukuran wajib diisi!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'data' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        if (Size::where('nama', $request->nama)->exists()) {
            return response([
                'message' => 'error',
                'data' => 'Nama sudah ada!',
                'status' => 400
            ], 400);
        } else {
            $category = Size::create([
                'nama' => $request->nama,
            ]);
        }

        return response([
            'message' => 'success',
            'data' => [
                'id' => $category->id,
                'nama' => $category->nama,
                'tanggal_input' => $category->created_at,
            ],
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function showById(Request $request, $id)
    {
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $categoryId = Size::where('id', $id)->first();

        if ($categoryId) {
            return response([
                'message' => 'success',
                'data' => $categoryId,
                'status' => 200
            ], 200);
        } else {
            return response([
                'message' => 'error',
                'data' => 'Data tidak ditemukan',
                'status' => 404
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Size::where('id', $id)->first();

            if (!$category) {
                return response()->json([
                    'message' => 'error',
                    'data' => 'Ukuran tidak ditemukan',
                    'status' => 404
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|min:3|max:100',
            ], [
                'nama.required' => 'Nama ukuran wajib diisi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                    'status' => 422
                ], 422);
            }

            if (Size::where('nama', $request->nama)->where('id', '!=', $id)->exists()) {
                return response()->json([
                    'message' => 'error',
                    'data' => 'Ukuran sudah tersedia!',
                    'status' => 400
                ], 400);
            } else {
                $category->nama = $request->nama;
                $category->save();
            }

            return response()->json([
                'message' => 'success',
                'data' => $category,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'data' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $category = Size::find($id);
            if (!$category) {
                return response()->json([
                    'message' => 'error',
                    'data' => 'Ukuran tidak ditemukan',
                    'status' => 404
                ], 404);
            }
            $category->delete();
            return response()->json([
                'message' => 'success',
                'data' => 'Ukuran berhasil dihapus',
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'data' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}

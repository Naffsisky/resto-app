<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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

            $category = Category::paginate(10);

            if ($category->isNotEmpty()) {
                return response([
                    'message' => 'success',
                    'data' => $category
                ], 200);
            } else {
                return response([
                    'message' => 'error',
                    'data' => 'Tidak ada data kategori'
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
            'nama.required' => 'Nama Kategori wajib diisi!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'data' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        if (Category::where('nama', $request->nama)->exists()) {
            return response([
                'message' => 'error',
                'data' => 'Nama sudah ada!',
                'status' => 400
            ], 400);
        } else {
            $category = Category::create([
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

        $category = Category::where('id', $id)->first();

        if ($category) {
            return response([
                'message' => 'success',
                'data' => $category,
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

    public function showBySearch(Request $request)
    {
        try {
            $search = $request->input('q', '');

            $category = Category::when($search, function ($query, $search) {
                $query->where('nama', 'LIKE', "%{$search}%");
            })->paginate(10);

            if ($category->total() === 0) {
                return response()->json([
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'message' => 'success',
                'data' => $category,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::where('id', $id)->first();

            if (!$category) {
                return response()->json([
                    'message' => 'error',
                    'data' => 'Kategori tidak ditemukan',
                    'status' => 404
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|min:3|max:100',
            ], [
                'nama.required' => 'Nama kategori wajib diisi',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation Error',
                    'errors' => $validator->errors(),
                    'status' => 422
                ], 422);
            }

            if (Category::where('nama', $request->nama)->where('id', '!=', $id)->exists()) {
                return response()->json([
                    'message' => 'error',
                    'data' => 'Kategori sudah tersedia!',
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
    public function destroy(string $id)
    {
        try {
            $category = Category::find($id);
            if (!$category) {
                return response()->json([
                    'message' => 'error',
                    'data' => 'Category tidak ditemukan',
                    'status' => 404
                ], 404);
            }
            $category->delete();
            return response()->json([
                'message' => 'success',
                'data' => 'Category berhasil dihapus',
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

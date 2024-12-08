<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
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

            $product = Product::with(['kategori', 'ukuran'])->paginate(10);

            if ($product->isNotEmpty()) {
                return response([
                    'message' => 'success',
                    'data' => $product,
                ], 200);
            } else {
                return response([
                    'message' => 'error',
                    'data' => 'Tidak ada data produk'
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
            'nama' => 'required|string|max:255',
            'harga' => 'required|integer',
            'gambar' => 'required|mimes:jpeg,png,jpg|max:2048',
            'variant' => 'required|string|max:255',
            'kategori_id' => 'required|exists:categories,id',
            'size_ids' => 'required|array',
            'size_ids.*' => 'exists:sizes,id',
            'tersedia' => 'required|boolean',
        ], [
            'nama.required' => 'Nama Produk wajib diisi!',
            'harga.required' => 'Harga Produk wajib diisi!',
            'gambar.required' => 'Gambar Produk wajib diisi!',
            'variant.required' => 'Variant Produk wajib diisi!',
            'kategori_id.required' => 'Kategori Produk wajib diisi!',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid!',
            'size_ids.required' => 'Ukuran Produk wajib diisi!',
            'size_ids.*.exists' => 'Ukuran yang dipilih tidak valid!',
            'tersedia.required' => 'Ketersediaan Produk wajib diisi!',
        ]);

        $gambarPath = $request->file('gambar')->store('products', 'public');

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'data' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        try {
            $product = Product::create([
                'nama' => $request->nama,
                'harga' => $request->harga,
                'gambar' => $gambarPath,
                'variant' => $request->variant,
                'kategori_id' => $request->kategori_id,
                'tersedia' => $request->tersedia,
            ]);

            // Menyambungkan produk dengan ukuran
            $product->ukuran()->sync($request->size_ids);

            return response()->json([
                'message' => 'success',
                'data' => [
                    'id' => $product->id,
                    'nama' => $product->nama,
                    'harga' => $product->harga,
                    'gambar' => asset('storage/' . $product->gambar),
                    'variant' => $product->variant,
                    'kategori' => $product->kategori ? $product->kategori->nama : null,
                    'ukuran' => $product->ukuran()->pluck('nama')->toArray(),
                    'tersedia' => $product->tersedia,
                    'created_at' => $product->created_at,
                ],
                'status' => 201
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'data' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
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

        $product = Product::with(['kategori', 'ukuran'])->find($id);

        if ($product) {
            return response([
                'message' => 'success',
                'data' => [
                    'id' => $product->id,
                    'nama' => $product->nama,
                    'harga' => $product->harga,
                    'gambar' => $product->gambar,
                    'variant' => $product->variant,
                    'kategori' => $product->kategori ? $product->kategori->nama : null,
                    'ukuran' => $product->ukuran()->pluck('nama')->toArray(),
                    'tersedia' => $product->tersedia,
                    'created_at' => $product->created_at,
                ],
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
        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|string|max:255',
            'harga' => 'sometimes|integer',
            'gambar' => 'sometimes|string|max:255',
            'variant' => 'sometimes|string|max:255',
            'kategori_id' => 'sometimes|exists:categories,id',
            'size_ids' => 'sometimes|array',
            'size_ids.*' => 'exists:sizes,id',
            'tersedia' => 'sometimes|boolean',
        ], [
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid!',
            'size_ids.*.exists' => 'Ukuran yang dipilih tidak valid!',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'data' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        try {
            $product = Product::findOrFail($id);
            $product->update($request->only(['nama', 'harga', 'gambar', 'variant', 'kategori_id', 'tersedia']));

            if ($request->has('size_ids')) {
                $product->ukuran()->sync($request->size_ids);
            }

            return response()->json([
                'message' => 'success',
                'data' => [
                    'id' => $product->id,
                    'nama' => $product->nama,
                    'harga' => $product->harga,
                    'gambar' => $product->gambar,
                    'variant' => $product->variant,
                    'kategori' => $product->kategori ? $product->kategori->nama : null,
                    'ukuran' => $product->ukuran()->pluck('nama')->toArray(),
                    'tersedia' => $product->tersedia,
                    'updated_at' => $product->updated_at,
                ],
                'status' => 200
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'error',
                'data' => 'Produk tidak ditemukan!',
                'status' => 404
            ], 404);
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
            $product = Product::findOrFail($id);

            if ($product->gambar) {
                Storage::disk('public')->delete($product->gambar);
            }

            $product->delete();

            return response()->json([
                'message' => 'Produk berhasil dihapus',
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

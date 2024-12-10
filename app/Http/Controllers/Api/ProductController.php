<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
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

        // $gambarPath = $request->file('gambar')->store('products', 'public');
        $gambarFile = $request->file('gambar');
        $extension = $gambarFile->getClientOriginalExtension();
        $gambarNama = time() . '_' . Str::random(10) . '.' . $extension;
        $gambarPath = $gambarFile->storeAs('products', $gambarNama, 'public');
        // Ambil nama file saja untuk disimpan ke database
        $gambarNamaOnly = basename($gambarPath);

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
                'gambar' => $gambarNamaOnly,
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
                    'gambar' => asset('storage/products/' . $product->gambar),
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

        $product = Product::where('id', $id)->first();

        if (!$product) {
            return response()->json([
                'message' => 'error',
                'data' => 'Produk tidak ditemukan',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:255',
            'harga' => 'sometimes|required|integer',
            'gambar' => 'sometimes|required|mimes:jpeg,png,jpg|max:2048',
            'variant' => 'sometimes|required|string|max:255',
            'kategori_id' => 'sometimes|required|exists:categories,id',
            'size_ids' => 'sometimes|required|array',
            'size_ids.*' => 'exists:sizes,id',
            'tersedia' => 'sometimes|required|boolean',
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

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        try {
            $product = Product::findOrFail($id);

            if ($request->hasFile('gambar')) {

                // Hapus file lama jika ada
                if ($product->gambar) {
                    $oldImagePath = 'products/' . $product->gambar;
                    if (Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                        Log::info('Old image deleted successfully:', ['path' => $oldImagePath]);
                    } else {
                        Log::warning('Old image not found:', ['path' => $oldImagePath]);
                    }
                }

                // Upload file baru
                $gambarFile = $request->file('gambar');
                $extension = $gambarFile->getClientOriginalExtension();
                $gambarNama = time() . '_' . Str::random(10) . '.' . $extension;
                $gambarPath = $gambarFile->storeAs('products', $gambarNama, 'public');
                Log::info('New image saved:', ['path' => $gambarPath]);

                $product->gambar = basename($gambarPath);
            }

            $product->fill($request->only(['nama', 'harga', 'variant', 'kategori_id', 'tersedia']));
            $product->save();

            if ($request->has('size_ids')) {
                $product->ukuran()->sync($request->size_ids);
            }

            return response()->json([
                'message' => 'success',
                'data' => [
                    'id' => $product->id,
                    'nama' => $product->nama,
                    'harga' => $product->harga,
                    'gambar' => asset('storage/products/' . $product->gambar),
                    'variant' => $product->variant,
                    'kategori' => $product->kategori ? $product->kategori->nama : null,
                    'ukuran' => $product->ukuran()->pluck('nama')->toArray(),
                    'tersedia' => $product->tersedia,
                    'updated_at' => $product->updated_at,
                ],
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
            $product = Product::findOrFail($id);

            if ($product->gambar) {
                Storage::disk('public')->delete('products/' . $product->gambar);
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

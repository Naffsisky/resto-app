<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        try {
            $product = Product::paginate(10);

            if ($product->isNotEmpty()) {
                return response([
                    'message' => 'success',
                    'data' => $product->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'nama' => $product->nama,
                            'harga' => $product->harga,
                            'gambar' => $product->gambar,
                            'variant' => $product->variant,
                            'kategori' => $product->kategori ? $product->kategori->nama : null,
                            'ukuran' => $product->ukuran ? $product->ukuran->nama : null,
                            'tersedia' => $product->tersedia,
                        ];
                    }),
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }
}

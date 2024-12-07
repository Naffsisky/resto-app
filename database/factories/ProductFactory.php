<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Category;
use App\Models\Size;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Product::class;
    public function definition(): array
    {
        return [
            'nama' => $this->faker->words(3, true), // Nama produk
            'harga' => $this->faker->numberBetween(10000, 50000), // Harga antara 10.000 - 50.000
            'gambar' => $this->faker->imageUrl(640, 480, 'food', true, 'makanan'), // URL gambar makanan
            'variant' => $this->faker->randomElement(['Pedas', 'Manis', 'Asin', 'Gurih']), // Variant random
            'kategori_id' => Category::inRandomOrder()->first()->id, // ID kategori acak
            'ukuran_id' => Size::inRandomOrder()->first()->id, // ID ukuran acak
            'tersedia' => $this->faker->boolean(80), // 80% tersedia
        ];
    }
}

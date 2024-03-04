<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductSale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductSale>
 */
class ProductSaleFactory extends Factory
{
    protected $model = ProductSale::class;

    public function definition()
    {
        $product = Product::factory()->create();
        return [
            'product_id' => $product->id,
            'amount' => $this->faker->numberBetween(1, 10),
            'price' => $product->price,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\ProductSale;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'total_value' => 0,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Sale $sale) {
            $productSales = ProductSale::factory()->count(3)->make();
            $sale->productsSale()->saveMany($productSales);

            $totalValue = $productSales->sum(function ($productSale) {
                return $productSale->price;
            });

            $sale->total_value = $totalValue;
            $sale->save();
        });
    }
}

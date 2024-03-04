<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductSale;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SaleControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testStoreSale()
    {
        $user = User::factory()->create();
        $this->seed(CategorySeeder::class);
        Product::factory(2)->create();

        $this->actingAs($user);

        $response = $this->postJson('/sales', [
            'products' => [
                [
                    'product_id' => 1,
                    'amount' => 2
                ],
                [
                    'product_id' => 2,
                    'amount' => 3
                ]
            ]
        ]);
        $response->assertStatus(201);

        $response->assertJsonStructure([
            'creation',
            'error'
        ]);
    }

    public function testIndexSale()
    {
        $user = User::factory()->create();
        $this->seed(CategorySeeder::class);
        Product::factory(2)->create();

        $this->actingAs($user);

        $this->postJson('/sales', [
            'products' => [
                [
                    'product_id' => 1,
                    'amount' => 2
                ],
                [
                    'product_id' => 2,
                    'amount' => 3
                ]
            ]
        ]);

        $response = $this->getJson('/sales');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'sales'
        ]);
    }

    public function testShowSale()
    {
        $user = User::factory()->create();
        $this->seed(CategorySeeder::class);
        Product::factory(2)->create();

        $this->actingAs($user);

        $this->postJson('/sales', [
            'products' => [
                [
                    'product_id' => 1,
                    'amount' => 2
                ],
                [
                    'product_id' => 2,
                    'amount' => 3
                ]
            ]
        ]);

        $response = $this->getJson('/sales/1');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'sale'
        ]);
    }

    public function testAddProductToSale(): void
    {
        $user = User::factory()->create();
        $sale = Sale::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create([
            'amount' => 5,
            'price' => 100,
        ]);

        $this->actingAs($user);

        $response = $this->putJson("/sales/{$sale->id}/addProduct", [
            'products' => [
                [
                    'product_id' => $product->id,
                    'amount' => 2,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'update' => true,
            'error' => '',
        ]);

        $this->assertDatabaseHas('product_sales', [
            'product_id' => $product->id,
            'sale_id' => $sale->id,
            'amount' => 2,
            'price' => 200,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'amount' => 3,
        ]);
    }

    public function testRemoveProductFromSale(): void
    {
        $user = User::factory()->create();
        $sale = Sale::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create([
            'amount' => 5,
            'price' => 100,
        ]);
        $productSale = ProductSale::factory()->create([
            'product_id' => $product->id,
            'sale_id' => $sale->id,
            'amount' => 2,
            'price' => 200,
        ]);

        $this->actingAs($user);

        $response = $this->deleteJson("/sales/{$sale->id}/removeProduct", [
            'products' => [
                [
                    'product_id' => $product->id,
                    'amount' => 1,
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'update' => true,
            'error' => '',
        ]);
    }

    public function testRemoveProductFromSaleWithInsufficientAmount(): void
    {
        $user = User::factory()->create();
        $sale = Sale::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create([
            'amount' => 5,
            'price' => 100,
        ]);
        $productSale = ProductSale::factory()->create([
            'product_id' => $product->id,
            'sale_id' => $sale->id,
            'amount' => 2,
            'price' => 200,
        ]);

        $this->actingAs($user);

        $response = $this->deleteJson("/sales/{$sale->id}/removeProduct", [
            'products' => [
                [
                    'product_id' => $product->id,
                    'amount' => 3,
                ],
            ],
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'update' => false,
            'error' => 'Product amount not available',
        ]);
    }

    public function testCancelSale(): void
{
    $user = User::factory()->create();
    $sale = Sale::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    $response = $this->deleteJson("/sales/{$sale->id}");

    $response->assertStatus(200);
    $response->assertJson([
        'delete' => true,
        'error' => '',
    ]);

    $this->assertDatabaseMissing('sales', [
        'id' => $sale->id,
    ]);
}
}

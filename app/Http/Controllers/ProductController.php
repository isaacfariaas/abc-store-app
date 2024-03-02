<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category; // Add this line

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get(); // Include the category relationship

        $formattedProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'description' => $product->description,
                'category' => $product->category->name,
                'amount' => $product->amount,
            ];
        });

        return response()->json([
            'products' => $formattedProducts,
        ]);
    }
}

<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\ProductSale;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('productsSale')->get();
        return response()->json(['sales' => $sales]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $sale = new Sale();
            $sale->user_id = Auth::user()->id;
            $total_value = 0;
            $sale->total_value = 0;
            $sale->save();

            if ($request->products) {
                foreach ($request->products as $product) {

                    if (!Product::find($product['product_id'])) {
                        return response()->json(['creation' => false, 'message' => 'Product not found'], 404);
                    }

                    $product_stock = Product::find($product['product_id']);
                    if ($product_stock->amount < $product['amount']) {
                        DB::rollback();
                        return response()->json(['creation' => false, 'error' => 'Product amount not available'], 404);
                    }
                    $total_price = $product_stock->price * $product['amount'];

                    $product_sale = new ProductSale();
                    $product_sale->product_id = $product['product_id'];
                    $product_sale->sale_id = $sale->id;
                    $product_sale->amount = $product['amount'];
                    $product_sale->price = $total_price;
                    $product_sale->save();

                    $total_value += $total_price;

                    $product_stock->amount -= $product['amount'];
                    $product_stock->save();
                }
            } else {
                DB::rollback();
                return response()->json(['creation' => false, 'error' => 'Products not found'], 404);
            }

            $sale->total_value = $total_value;
            $sale->save();

            DB::commit();
            return response()->json(['creation' => true, 'error' => ''], 200);
        } catch (\Exception $e) {

            DB::rollback();

            return response()->json(['creation' => false, 'error' => $e->getMessage()], 400);
        }
    }
    
    public function show($id)
    {
        $sale = Sale::with(['productsSale', 'productsSale.product', 'productsSale.product.category', 'user'])->find($id);
        if ($sale) {
            return response()->json(['sale' => $sale]);
        } else {
            return response()->json(['message' => 'Sale not found'], 404);
        }
    }
}

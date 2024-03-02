<?php

namespace App\Http\Controllers;

use App\Models\Sale;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('productsSale')->get();
        return response()->json(['sales' => $sales]);
    }
    
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockUsageTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{

    public function create(Request $request) {
        $this->validate($request, [
            'title' => 'required|max:50|unique:products|string',
            'rate' => 'required|numeric|min:1'
        ]);

        $product = Product::create([
            'title' => $request->input('title'),
            'rate' => $request->input('rate')
        ]);
        return response()->json($product);
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1',
            'title' => 'required|max:50|string',
            'rate' => 'required|numeric|min:1',
        ]);

        $product = Product::findOrFail($request->input('id'));
        $product->title = $request->input('title');
        $product->rate = $request->input('rate');
        $product->save();

        Cache::forget('products');

        return response()->json($product);
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->input('id'));
        $product->delete();

        Cache::forget('products');

        return response()->json(['message'=>'Product Deleted Successfully']);
    }

    public function addStockTempate(Request $request) {
        $this->validate($request, [
            'stock_item_id' => 'required|exists:App\Models\Stock,id',
            'product_id' => 'required|exists:App\Modesl\Product,id',
            'quantity' => 'required|numeric|min:0.1'
        ]);
        return response()->json(StockUsageTemplate::new([
            $request->stock_item_id,
            $request->product_id,
            $request->quantity
        ]));
    }

        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\GeneralItemService;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

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

    public function getGeneralItems() {
        $generalItems = Cache::remember('generalItem', 3600, function() {
            return GeneralItemService::getItems();
        });

        return response()->json($generalItems);
    }
}

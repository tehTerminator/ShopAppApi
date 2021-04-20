<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
            'title' => 'required|max:50|unique:products|string',
            'rate' => 'required|numeric|min:1',
        ]);

        $product = Product::findOrFail($request->input('id'));
        $product->title = $request->input('title');
        $product->title = $request->input('rate');
        $product->save();

        return response()->json($product);
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->input('id'));
        $product->delete();

        return response()->json(['message'=>'Product Deleted Successfully']);
    }
}

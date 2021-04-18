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
            'title' => 'required|max:50|unique:products|alpha_num',
            'rate' => 'required|numeric|min:1'
        ]);

        $payload = $request->input('title', 'rate');
        $product = Product::create($payload);
        return response()->json($product);
}
}

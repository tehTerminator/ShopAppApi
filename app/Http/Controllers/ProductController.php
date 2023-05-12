<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\GeneralItemService;
use App\Services\ValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
        $this->validateRequest($request);
        $product = Product::create($request->only(['title', 'rate']));
        Cache::forget('products');
        return response()->json($product);
    }

    public function update(Request $request) {
        $this->validateRequest($request, 'update');
        $product = Product::findOrFail($request->input('id'));
        $product->update($request->only(['title', 'rate']));
        $product->save();
        Cache::forget('products');
        return response()->json($product);
    }

    public function delete(Request $request) {
        $this->validateRequest($request, 'delete');
        Product::findOrFail($request->input('id'))->delete();
        Cache::forget('products');
        return response()->json(['message'=>'Product Deleted Successfully']);
    }

    public function getGeneralItems() {
        $generalItems = Cache::remember('generalItem', 600, function() {
            return GeneralItemService::getItems();
        });
        return response()->json($generalItems);
    }


    private function validateRequest(Request $request, $requestType = 'create') {
        $validated = ValidationService::validateModel($request, 'products', $requestType);

        if (!$validated) {
            return response('Invalid Data Received', 201);
        }

        return;
    }
}

<?php

namespace App\Services;

use App\Models\Product;

class ProductService extends BaseService {

    public function update(int $id, string $title, float $rate) {
        $product = Product::findOrFail($id);
        $product->title = $title;
        $product->rate = $rate;
        return $product->save()->refresh();
    }

    public function __construct() {
        self::$validationRules = [
            'id' => ['numeric'],
            'title' => ['string', 'required', 'size:30'],
            'rate' => ['required', 'numeric', 'min:0.1']
        ];
    }
}
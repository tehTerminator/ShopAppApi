<?php

namespace App\Services;

use App\Models\Stock;

class StockService {

    public static $validationRules = [
        'id' => ['required', 'numeric'],
        'title' => ['required', 'unique:App\Models\Stock,title'],
        'quantity' => ['required', 'numeric', 'min:0']
    ];

    public function __construct() { }

    public static function create(string $title, int $quantity) {
        $stock = Stock::create([
            'title' => $title,
            'quantity' => $quantity
        ]);

        return $stock;
    }

    public static function update(int $id, string $title) {
        $stock = Stock::findOrFail($id);
        if ($stock->title === $title) {
            return $stock;
        }

        $stock->title = $title;
        $stock->save();
        $stock->refresh();
        return $stock;
    }

    public static function delete(int $id) {
        $stock = Stock::findOrFail($id);
        $stock->delete();
    }

    public static function getValidationRules($uniqueTitle = true) {
        if ($uniqueTitle) {
            return StockService::$validationRules;
        }

        $rules = StockService::$validationRules;
        unset($rules['title'][1]);
        return $rules;
    }
}
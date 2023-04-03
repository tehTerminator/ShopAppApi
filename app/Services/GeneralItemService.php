<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Ledger;
use App\Models\PosItem;

class GeneralItemService
{

    private static $TYPE = [
        'PRODUCT' => 0,
        'LEDGER' => 1,
        'BUNDLE' => 2
    ];

    public static function getItems()
    {
        $products = Product::select('id', 'title', 'rate', 'created_at', 'updated_at')->get();
        $ledgers = Ledger::select('id', 'title', 'created_at', 'updated_at')->whereIn('kind', ['BANK', 'CASH', 'WALLET'])->get();
        $bundles = PosItem::select('id', 'title', 'rate', 'created_at', 'updated_at')->get();

        $items = [];

        foreach ($products as $item) {
            $item['type'] = self::$TYPE['PRODUCT'];
            array_push($items, $item);
        }

        foreach ($ledgers as $item) {
            $item['type'] = self::$TYPE['LEDGER'];
            $item['rate'] = 0;
            array_push($items, $item);
        }

        foreach ($bundles as $item) {
            $item['type'] = self::$TYPE['BUNDLE'];
            array_push($items, $item);
        }

        return $items;
    }
}

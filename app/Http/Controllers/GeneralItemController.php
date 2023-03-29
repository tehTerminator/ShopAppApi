<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ledger;
use App\Models\Bundle;
use Illuminate\Support\Facades\Cache;

class GeneralItemController extends Controller {

    private $TYPE = [
        'PRODUCT' => 0,
        'LEDGER' => 1,
        'BUNDLE' => 2,
    ];

    public function select() {

        $generalItems = Cache::remember('generalItem', 3600, function() {
            $products = Product::select('id', 'title', 'rate')->get();
            $ledgers = Ledger::select('id', 'title')->whereIn('kind', ['BANK', 'CASH', 'WALLET'])->get();
            $bundles = Bundle::select('id', 'title', 'rate')->get();

            $items = [];

            foreach($products as $item) {
                $item['type'] = $this->TYPE['PRODUCT'];
                array_push($items, $item);
            }

            foreach($ledgers as $item) {
                $item['type'] = $this->TYPE['LEDGER'];
                $item['rate'] = 0;
                array_push($items, $item);
            }

            foreach($bundles as $item) {
                $item['type'] = $this->TYPE['BUNDLE'];
                array_push($items, $item);
            }

            return $items;
        });

        return response()->json($generalItems);
    }

    public function removeCache() {
        Cache::delete('generalItem');
        return response('Cache removed');
    }

    public function __costruct() {}
}
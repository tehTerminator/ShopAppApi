<?php 

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ProductGraphDataService {

    function __construct(){}

    public function productWiseSalesCount($date) {
        $response = Cache::remember('productWiseSaleCount' . $date, 30, function() use ($date) {
            return Transaction::select(
                DB::raw(
                    "FLOOR(sum(
                        `transactions`.`quantity` * `transactions`.`rate` * ( 1 - `transactions`.`discount` / 100)
                    )) as 'value'"),
                DB::raw("products.title as 'name'")
            )
            ->whereDate('transactions.created_at', $date)
            ->where('item_type', 'PRODUCT')
            ->leftJoin('products', 'products.id', '=', 'transactions.item_id')
            ->groupBy('name')
            ->get();
        });
        return $response;
    }
}
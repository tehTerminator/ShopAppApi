<?php 

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ProductGraphDataService {
    private $date;

    function __construct()
    {
        $this->date = Carbon::now();
    }

    public function productWiseSalesCount($date) {
        $this->date = $date;
        $response = Cache::remember('productWiseSaleCount' . $this->date, 300, function() {
            return Transaction::select(
                DB::raw(
                    "sum(
                        calcTransactionAmount(
                            `transactions`.`quantity`, `transactions`.`rate`, `transactions`.`discount`
                        )
                    ) as 'value'"),
                DB::raw("products.title as 'name'")
            )
            ->whereDate('transactions.created_at', $this->date)
            ->where('item_type', 'PRODUCT')
            ->join('products', 'products.id', '=', 'transactions.item_id')
            ->groupBy('name')
            ->get();
        });

        return $response;
    }
}
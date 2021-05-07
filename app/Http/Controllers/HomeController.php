<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
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

    public function userWiseInvoiceCount() {
        $response = Cache::remember('userWiseInvoiceCount', 300, function() {
            return Invoice::select(
                DB::raw('count(invoices.id) as value'),
                DB::raw('users.displayName as name'),
            )
            ->whereDate('invoices.created_at', Carbon::now())
            ->join('users', 'users.id', '=', 'invoices.user_id')
            ->groupBy('name')
            ->get();
        });

        return response()->json($response);
    }

    public function userWisePaymentCount() {
        $response = Cache::remember('userWisePaymentCount', 300, function(){
            return Transaction::select(
                DB::raw(
                "sum(
                    calcTransactionAmount(
                        `transactions`.`quantity`, `transactions`.`rate`, `transactions`.`discount`
                    )
                ) as 'value'"),
                DB::raw("users.displayName as 'name'"),
            )->whereDate('transactions.created_at', Carbon::now())
            ->where('transactions.item_type', 'LEDGER')
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->groupBy('name')
            ->get();
        });
        // ->toSql();
                
        // return response($response);
        return response()->json($response);
    }

    public function userWiseSalesCount() {
        $response = Cache::remember('userWiseSalesCount', 300, function() {
            return Transaction::select(
                DB::raw(
                "sum(
                    calcTransactionAmount(
                        `transactions`.`quantity`, `transactions`.`rate`, `transactions`.`discount`
                    )
                ) as 'value'"),
                DB::raw("users.displayName as 'name'"),
            )->whereDate('transactions.created_at', Carbon::now())
            ->where('transactions.item_type', 'PRODUCT')
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->groupBy('name')
            ->get();
        });
        // ->toSql();
                
        // return response($response);
        return response()->json($response);
    }

    public function productWiseSaleCount() {
        $response = Cache::remember('productWiseSaleCount', 300, function() {
            return Transaction::select(
                DB::raw(
                    "sum(
                        calcTransactionAmount(
                            `transactions`.`quantity`, `transactions`.`rate`, `transactions`.`discount`
                        )
                    ) as 'value'"),
                DB::raw("products.title as 'name'")
            )
            ->whereDate('transactions.created_at', Carbon::now())
            ->where('item_type', 'PRODUCT')
            ->join('products', 'products.id', '=', 'transactions.item_id')
            ->groupBy('name')
            ->get();
        });

        return response()->json($response);
    }

    public function monthlyStats() {
        $invoices = Cache::remember('monthlyStats', 3600, function(){
            $date = Carbon::now()->subDays(30);
            return Invoice::select(
                DB::raw('sum(amount) as value'),
                DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as name")
            )
            ->whereDate('created_at', '>', $date)
            ->groupBy('name')
            ->get();
        });
        // return response($invoices);
        return response()->json($invoices);
    }
}

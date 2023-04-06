<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserReportController extends Controller
{
    private $date = NULL;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->date = Carbon::now();
    }

    public function userWiseInvoiceCount(Request $request)
    {

        $date = $request->input('date', Carbon::now());

        $response = Cache::remember('userWiseInvoiceCount' . $this->date, 300, function () use ($date){
            return Invoice::select(
                DB::raw('count(invoices.id) as value'),
                DB::raw('users.displayName as name'),
            )
                ->whereDate('invoices.created_at', $date)
                ->join('users', 'users.id', '=', 'invoices.user_id')
                ->groupBy('name')
                ->get();
        });

        return response()->json($response);
    }

    public function userWisePaymentCount(Request $request)
    {

        $date = $request->input('date', Carbon::now());

        $response = Cache::remember('userWisePaymentCount' . $date, 300, function () use ($date) {
            return Transaction::select(
                DB::raw(
                    "FLOOR(
                        SUM(
                            transactions.quantity * transactions.rate * (1 - transactions.discount / 100)
                        ) 
                    ) as 'value'"
                ),
                DB::raw("users.displayName as 'name'"),
            )
                ->whereDate('transactions.created_at', $date)
                ->where('transactions.item_type', 'LEDGER')
                ->join('users', 'users.id', '=', 'transactions.user_id')
                ->groupBy('name')
                ->get();
        });
        // ->toSql();

        // return response($response);
        return response()->json($response);
    }

    public function userWiseSalesCount(Request $request)
    {

        $date = $request->input('date', Carbon::now());

        $response = Cache::remember('userWiseSalesCount' . $date, 300, function () use ($date) {
            return Transaction::select(
                DB::raw(
                    "FLOOR(
                        SUM(
                            transactions.quantity * transactions.rate * ( 1 - transactions.discount / 100)
                )) as 'value'"
                ),
                DB::raw("users.displayName as 'name'"),
            )
                ->whereDate('transactions.created_at', $date)
                ->where('transactions.item_type', 'PRODUCT')
                ->join('users', 'users.id', '=', 'transactions.user_id')
                ->groupBy('name')
                ->get();
        });
        // ->toSql();

        // return response($response);
        return response()->json($response);
    }
}

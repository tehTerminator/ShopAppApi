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

    public function userWiseInvoiceCount(Request $request) {
        
        if ($request->has('date')) {
            $this->date = $request->input('date');
        }

        $response = Cache::remember('userWiseInvoiceCount' . $this->date, 300, function() {
            return Invoice::select(
                DB::raw('count(invoices.id) as value'),
                DB::raw('users.displayName as name'),
            )
            ->whereDate('invoices.created_at', $this->date)
            ->join('users', 'users.id', '=', 'invoices.user_id')
            ->groupBy('name')
            ->get();
        });

        return response()->json($response);
    }

    public function userWisePaymentCount(Request $request) {
        
        if ($request->has('date')) {
            $this->date = $request->input('date');
        }

        $response = Cache::remember('userWisePaymentCount' . $this->date, 300, function(){
            return Transaction::select(
                DB::raw(
                "sum(
                    calcTransactionAmount(
                        `transactions`.`quantity`, `transactions`.`rate`, `transactions`.`discount`
                    )
                ) as 'value'"),
                DB::raw("users.displayName as 'name'"),
            )
            ->whereDate('transactions.created_at', $this->date)
            ->where('transactions.item_type', 'LEDGER')
            ->join('users', 'users.id', '=', 'transactions.user_id')
            ->groupBy('name')
            ->get();
        });
        // ->toSql();
                
        // return response($response);
        return response()->json($response);
    }

    public function userWiseSalesCount(Request $request) {
        
        if ($request->has('date')) {
            $this->date = $request->input('date');
        }

        $response = Cache::remember('userWiseSalesCount' . $this->date, 300, function() {
            return Transaction::select(
                DB::raw(
                "sum(
                    calcTransactionAmount(
                        `transactions`.`quantity`, `transactions`.`rate`, `transactions`.`discount`
                    )
                ) as 'value'"),
                DB::raw("users.displayName as 'name'"),
            )
            ->whereDate('transactions.created_at', $this->date)
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

<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Ledger;
use App\Models\Transaction;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
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

    public function productWiseSaleCount(Request $request) {
        
        if ($request->has('date')) {
            $this->date = $request->input('date');
        }

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

        return response()->json($response);
    }

    public function monthlyStats() {
        $invoices = Cache::remember('monthlyStats', 3600, function(){
            $date = Carbon::now()->subDays(30);
            return Invoice::select(
                DB::raw('sum(amount) as value'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as name")
            )
            ->whereDate('created_at', '>', $date)
            ->groupBy('name')
            ->orderBy('name', 'ASC')
            ->get();
        });
        // return response($invoices);
        return response()->json($invoices);
    }

    public function incomeExpense() {
        $this->date = Carbon::now()->subDays(30);
        $graphData = Cache::remember('incomeExpense', 3600, function() {
            $data = [
                [
                    'name' => 'Income',
                    'series' => []
                ],
                [
                    'name' => 'Expense',
                    'series' => []
                ]
            ];
    
            $incomeLedgers = Ledger::where('kind', 'INCOME')->pluck('id')->toArray();
            $expenseLedgers = Ledger::where('kind', 'EXPENSE')->pluck('id')->toArray();
    
            $data[0]['series'] = Voucher::select(
                DB::raw("sum(amount) as 'value'"),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as name")
            )
            ->whereIn('cr', $incomeLedgers)
            ->whereDate('created_at', '>', $this->date)
            ->groupBy('name')
            ->orderBy('name', 'ASC')
            ->get();
    
            $data[1]['series'] = Voucher::select(
                DB::raw("sum(amount) as 'value'"),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as name")
            )
            ->whereIn('dr', $expenseLedgers)
            ->whereDate('created_at', '>', $this->date)
            ->groupBy('name')
            ->orderBy('name', 'ASC')
            ->get();
    
            return $data;
        });

        return response()->json($graphData);
        
    }
}

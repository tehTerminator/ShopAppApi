<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\Ledger;
use App\Models\Voucher;

class GraphDataService {
    private $date = NULL;

    public function __construct()
    {
        $this->date = Carbon::now();
    }

    public function monthlyStats() {
        $response = Cache::remember('monthlyStats', 3600, function(){
            $date = Carbon::now()->subDays(7);
            return Invoice::select(
                DB::raw('sum(amount) as value'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as name")
            )
            ->whereDate('created_at', '>', $date)
            ->groupBy('name')
            ->orderBy('name', 'ASC')
            ->get();
        });
        
        return $response();
    }

    public function incomeVsExpenses() {
        $this->date = Carbon::now()->subDays(7);
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

        return $graphData;
    }

    public function userWiseInvoiceCount() {
        
    }
}
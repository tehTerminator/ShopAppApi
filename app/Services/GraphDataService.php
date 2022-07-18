<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\Ledger;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Voucher;

class GraphDataService
{
    private $date;

    public function __construct()
    {
        $this->date = Carbon::now();
    }

    public function monthlyInvoiceAmount()
    {
        $response = Cache::remember('monthlyStats', 3600, function () {
            $date = Carbon::now()->subDays(15);
            return Invoice::select(
                DB::raw('sum(amount) as value'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as name")
            )
                ->whereDate('created_at', '>', $date)
                ->groupBy('name')
                ->orderBy('name', 'ASC')
                ->get();
        });

        return $response;
    }

    public function incomeVsExpenses()
    {
        $this->date = Carbon::now()->subDays(15);
        $graphData = Cache::remember('incomeExpense', 3600, function () {
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

    public function operatorMonthlyComparison()
    {
        $this->date = Carbon::now()->subDays(30);
        $graphData = Cache::remember('operatorSalesComparison', 0, function () {
            $data = [];

            $operators = User::all();

            foreach ($operators as $operator) {
                $operatorData = [
                    'name' => $operator->displayName,
                    'series' => $this->operatorMonthlyReport($operator->id)
                ];
                array_push($data, $operatorData);
            }
            return $data;
        });
        return $graphData;
    }

    private function operatorMonthlyReport($id)
    {
        return Transaction::select(
            DB::raw("
            sum(
                calcTransactionAmount(
                    transactions.quantity, 
                    transactions.rate, 
                    transactions.discount
                )
            ) as value"),
            DB::raw("date(transactions.created_at) as name"),
        )->whereDate('created_at', '>=', $this->date)
            ->where('user_id', $id)
            ->where('item_type', 'PRODUCT')
            ->groupBy('name')
            ->orderBy('created_at', 'ASC')
            ->get();
    }
}

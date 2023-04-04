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
        $response = Cache::remember('monthlyStats', 43200, function () {
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
        $graphData = Cache::remember('incomeExpense', 43200, function () {
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

            $data[0]['series'] = $this->cumulateSeries('cr', $incomeLedgers);
            $data[1]['series'] = $this->cumulateSeries('dr', $expenseLedgers);

            return $data;
        });

        return $graphData;
    }

    private function cumulateSeries($crOrDr, $idList) {
        $response = [];
        $total = 0;
        for($i = 30; $i > 0; $i--) {
            $forDate = Carbon::now()->subDays($i)->format('Y-m-d');
            $total += $this->getAmountForDate($crOrDr, $idList, $forDate);
            array_push($response, ["name" => $forDate, "value" => $total]);
        }
        return $response;
    }

    private function getAmountForDate($crOrDr, $idList, $date) {
        $voucher = Voucher::select(
            DB::raw("sum(amount) as 'value'"),
            DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as name")
        )
            ->whereIn($crOrDr, $idList)
            ->whereDate('created_at', $date)
            ->groupBy('name')
            ->orderBy('name', 'ASC')
            ->first();
        if(empty($voucher)) {
            return 0;
        }
        return $voucher->value;
    }

    public function operatorMonthlyComparison()
    {
        $graphData = Cache::remember('operatorComparison', 43200, function () {
            $data = [];

            $operators = User::whereIn('id', '>', 1)->get();

            foreach ($operators as $operator) {
                $operatorData = [
                    'name' => $operator->displayName,
                    'series' => $this->cumulateData($operator->id)
                ];
                array_push($data, $operatorData);
            }
            return $data;
        });
        return $graphData;
    }

    private function cumulateData($id) {
        $response = [];
        $total = 0;
        for($i = 30; $i > 0; $i--) {
            $forDate = Carbon::now()->subDays($i)->format('Y-m-d');
            $total += $this->operatorDailyReport($id, $i);
            array_push($response, ["name" => $forDate, "value" => $total]);
        }
        return $response;
    }

    private function operatorDailyReport($id, $days)
    {
        $theDate = Carbon::now()->subDays($days);
        $row = Transaction::select(
            DB::raw("
            sum(
                transaction.quantity * transaction.rate * ( 1 - transaction.discount /100)
            )
            as value"),
            DB::raw("date(transactions.created_at) as name"),
        )->whereDate('created_at', $theDate)
            ->where('user_id', $id)
            ->where('item_type', 'PRODUCT')
            ->groupBy('name')
            ->orderBy('created_at', 'ASC')
            ->first();

        if(empty($row)) {
            return 0;
        }
        return $row->value;
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\GraphDataService;
use App\Services\ProductGraphDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $this->date = $request->input('date', Carbon::now());
        $service = new ProductGraphDataService();
        $response = $service->productWiseSalesCount($this->date);
        return response()->json($response);
    }

    public function monthlyStats() {
        return response()->json(GraphDataService::monthlyInvoiceAmount());
    }

    public function incomeExpense() {
        return response()->json(GraphDataService::incomeVsExpenses());
    }
    
    public function operatorPerformance() {
        return response()->json(GraphDataService::operatorMonthlyComparison());
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BalanceSnapShot;
use App\Models\Ledger;
use Illuminate\Http\Request;
use App\Services\LedgerService;
use Carbon\Carbon;

class LedgerController extends Controller
{
    private $ledgerService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function select() {
        return response()->json(Ledger::all());
    }

    public function create(Request $request) {
        $this->validate($request, LedgerService::getValidationRules(true, false));
        
        return response()->json(
            LedgerService::createLedger(
                $request->title,
                $request->kind,
                $request->input('balance', 0),
                $request->input('canReceivePayment', false)
            ));
    }

    public function update(Request $request) {
        $this->validate(
            $request, LedgerService::getValidationRules(false, true)
        );
        return response()->json(
            LedgerService::updateLedger(
                $request->id,
                $request->title,
                $request->input('kind'),
                $request->input('canReceivePayment')
            )
        );
    }

    public function takeBalanceSnapshot() {
        return response()->json(LedgerService::takeBalanceSnapshot());
    }

    public function selectBalance(Request $request) {
        $date = $request->query('date', Carbon::now());
        $data = BalanceSnapshot::whereDate('created_at', $date)
        ->with(['ledger'])->get();
        return response($data);
    }
}

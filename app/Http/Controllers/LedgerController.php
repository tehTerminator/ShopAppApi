<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use Illuminate\Http\Request;
use App\Services\LedgerService;

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
        $this->ledgerService = new LedgerService();
    }

    public function select(Request $request) {
        $ledgers = 0;
        if($request->hasAny('id')) {
            $id = $request->query('id');
            $ledgers = $this->ledgerService->getLedgerById($id);
        } else {
            $ledgers = $this->ledgerService->getAllLedgers();
        }
        return response()->json($ledgers);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'title' => 'required|unique:ledgers|max:50|min:3|string',
            'kind' => 'required|in:BANK,CASH,PAYABLES,RECEIVABLES,EXPENSE,INCOME',
        ]);

        $ledger = $this->ledgerService->createLedger($request->title, $request->kind);
        return response()->json($ledger);
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer',
            'title' => 'required|unique:ledgers|max:50|min:3|string',
            'kind' => 'required|in:BANK,CASH,PAYABLES,RECEIVABLES,EXPENSE,INCOME',
        ]);

        $ledger = $this->ledgerService->updateLedger(
            $request->id,
            $request->title,
            $request->kind
        );
        
        return response()->json($ledger);
    }

    public function updateBalance(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer',
            'opening' => 'numeric',
            'closing' => 'numeric',
        ]);

        $id = $request->input('id');
        $opening = $request->has('opening') ? $request->opening : 0;
        $closing = $request->has('closing') ? $request->closing : 0;

        $ledger = $this->ledgerService->updateBalance($id, $opening, $closing);
        return response()->json($ledger);
    }

    public function selectBalance(Request $request, int $id) {
        $balance = Balance::whereDate('created_at', $request->query('date'))
        ->where('ledger_id', $id)->first();
        return response()->json($balance);
    }
}

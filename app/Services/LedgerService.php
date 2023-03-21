<?php

namespace App\Services;

use App\Models\Ledger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\BalanceSnapShot;
use App\Models\Voucher;
use Illuminate\Http\Request;

class LedgerService {
    public function createLedger(
        string $title, 
        string $kind, 
        int $balance, 
        $can_receive_payment = false
    ) {
        $ledger = Ledger::create([
            'title' => $title,
            'kind' => $kind,
            'balance' => $balance,
            'can_receive_payment' => $can_receive_payment
        ]);
        return $ledger;
    }

    public function updateLedger(
        int $id, 
        string $title, 
        string $kind
    ) {
        $ledger = Ledger::findOrFail($id);
        $ledger->title = $title;
        $ledger->kind = $kind;
        $ledger->save();
        $ledger->refresh();
        return $ledger;
    }

    public function takeBalanceSnapshot() {
        $ledgers = Ledger::all();
        foreach ($ledgers as $ledger) {
            # code...
            $this->takeSnapshotofLedger($ledger);
        }

        return BalanceSnapshot::whereDate('created_at', Carbon::now())
        ->with('ledger')->get();
    }


    public function takeSnapshotofLedger(Ledger $ledger) {
        $row = BalanceSnapshot::where('ledger_id', $ledger->id)
        ->whereDate('created_at', Carbon::now())
        ->first();
        if(empty($row)) {
            BalanceSnapShot::new([
                'ledger_id' => $ledger->id,
                'balance' => $ledger->balance
            ]);
        } else {
            $row->balance = $ledger->balance;
        }
    }

    public function __construct(){ }
}
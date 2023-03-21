<?php

namespace App\Services;

use App\Models\Ledger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\BalanceSnapShot;
use App\Models\Voucher;
use Illuminate\Http\Request;

class LedgerService {
    public function createLedger(string $title, string $kind) {
        $ledger = Ledger::create([
            'title' => $title,
            'kind' => $kind
        ]);
        return $ledger;
    }

    public function updateLedger(int $id, string $title, string $kind) {
        $ledger = Ledger::findOrFail($id);
        $ledger->title = $title;
        $ledger->kind = $kind;
        $ledger->save();
        $ledger = Ledger::find($id);
        return $ledger;
    }

    public function autoUpdateBalance() {
        $ledgers = Ledger::whereIn('kind', ['CASH', 'BANK'])->get();
        foreach ($ledgers as $ledger) {
            # code...
            $this->autoSetBalanceById($ledger->id);
        }

        return BalanceSnapshot::whereDate('created_at', Carbon::now())
        ->with('ledger')->get();
    }

    public function updateBalance(int $id, $opening, $closing) {
        $balance = BalanceSnapshot::whereDate('created_at', Carbon::now())
        ->where('ledger_id', $id)->first();

        if (!$balance) {
            BalanceSnapshot::create([
                'ledger_id' => $id,
                'opening' => $opening,
                'closing' => $closing
            ]);
        } else {
            $balance->opening = $opening;
            $balance->closing = $closing;
            $balance->save();
        }

        $ledger = Ledger::find($id);
        Cache::forget('ledgers');
        return $ledger;
    }

    public function autoSetBalanceById(int $ledger_id) {
        $balance = BalanceSnapshot::where('ledger_id', $ledger_id)
        ->whereDate('created_at', Carbon::now())
        ->first();
        if(empty($balance)) {
            $opening  = $this->getLatestClosing($ledger_id);
        } else {
            $opening = $balance->opening;
        }
        $credit = $this->reduceAmount($ledger_id);
        $debit = $this->increaseAmount($ledger_id);
        $closing = $opening - $credit + $debit;
        $ledger = $this->updateBalance($ledger_id, $opening, $closing);
        return $ledger;
    }

    public function getLatestClosing(int $ledger_id) {
        $yesterday = Carbon::now()->subDays(1);
        $balance  = BalanceSnapshot::where('ledger_id', $ledger_id)
        ->whereDate('created_at', $yesterday)->first();
        if(empty($balance)) {
            return 0;
        }
        return $balance->closing;
    }

    private function reduceAmount(int $ledger_id) {
        $creditAmount = Voucher::where('cr', $ledger_id)
        ->whereDate('created_at', Carbon::now())
        ->sum('amount');
        return $creditAmount;
    }

    private function increaseAmount(int $ledger_id) {
        $debitAmount = Voucher::where('dr', $ledger_id)
        ->whereDate('created_at', Carbon::now())
        ->sum('amount');
        return $debitAmount;
    }

    public function __construct(){ }
}
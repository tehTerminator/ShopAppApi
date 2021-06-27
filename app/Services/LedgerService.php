<?php

namespace App\Services;

use App\Models\Ledger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Balance;
use App\Models\Voucher;
use Illuminate\Http\Request;

class LedgerService {
    public function getLedgerById(int $id) {
        $ledgers = Ledger::where('id', $id)
        ->with(['balance' => function($query) {
            $query
            ->whereDate('created_at', Carbon::now());
        }])
        ->first();

        return $ledgers;
    }

    public function getAllLedgers() {
        $ledgers = Cache::remember('ledgers', 3600, function(){
            return Ledger::with(
                ['balance' => function($query) {
                    $query
                    ->whereDate('created_at', Carbon::now());
                }
            ])->get();
        });

        return $ledgers;
    }

    public function createLedger(string $title, string $kind) {
        $ledger = Ledger::create([
            'title' => $title,
            'kind' => $kind
        ]);

        Cache::forget('ledgers');

        return $ledger;
    }

    public function updateLedger(int $id, string $title, string $kind) {
        $ledger = Ledger::findOrFail($id);
        $ledger->title = $title;
        $ledger->kind = $kind;
        $ledger->save();
        $ledger = $this->getLedgerById($id);
        Cache::forget('ledgers');
        return $ledger;
    }

    public function updateBalance(Request $request) {
        $id = $request->input('id');
        $opening = $request->input('opening', 0);
        $closing = $request->input('closing', 0);
        $ledger = NULL;
        if ($request->has(['opening', 'closing'])) {
            $ledger = $this->updateOpeningAndClosing($id, $opening, $closing);
        }
        else {
            $ledger = $this->autoSetOpeningBalance($id);
        }
        return $ledger;
    }

    private function updateOpeningAndClosing(int $id, $opening, $closing) {
        $balance = Balance::whereDate('created_at', Carbon::now())
        ->where('ledger_id', $id)->first();

        if (!$balance) {
            Balance::create([
                'ledger_id' => $id,
                'opening' => $opening,
                'closing' => $closing
            ]);
        } else {
            $balance->opening = $opening;
            $balance->closing = $closing;
            $balance->save();
        }

        $ledger = $this->getLedgerById($id);
        Cache::forget('ledgers');
        return $ledger;
    }

    public function autoSetOpeningBalance(int $ledger_id) {
        $balance = Balance::where('ledger_id', $ledger_id)
        ->whereDate('created_at', Carbon::now())
        ->first();
        if(empty($balance)) {
            $opening  = $this->getLatestClosing($ledger_id);
            $credit = $this->reduceAmount($ledger_id);
            $debit = $this->increaseAmount($ledger_id);
            $closing = $opening - $credit + $debit;
            $ledger = $this->updateOpeningAndClosing($ledger_id, $opening, $closing);
            return $ledger;
        } else {
            $opening = $balance->opening;
            $credit = $this->reduceAmount($ledger_id);
            $debit = $this->increaseAmount($ledger_id);
            $closing = $opening - $credit + $debit;
            $ledger = $this->updateOpeningAndClosing($ledger_id, $opening, $closing);
        }
    }

    public function getLatestClosing(int $ledger_id) {
        $balance  = Balance::where('ledger_id', $ledger_id)
        ->whereDate('created_at', '<', Carbon::now())
        ->orderBy('id', 'DESC')->first();
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
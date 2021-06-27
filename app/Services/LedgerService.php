<?php

namespace App\Services;

use App\Models\Ledger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Balance;

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

    public function updateBalance(int $id, $opening, $closing) {
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
        return response()->json($ledger);
    }

    public function __construct(){ }
}
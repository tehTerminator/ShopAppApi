<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\Balance;
use Illuminate\Support\Facades\Auth;

class VoucherService {

    public function select(int $id, $date) {
        $voucher = Voucher::whereDate('created_at', $date)
        ->where('state', 1)
        ->where(function($query, $id) {
            $query->where('cr', $id)
            ->orWhere('dr', $id);
        })->with(['creditor', 'debtor'])
        ->get();

        $opening = Balance::where('ledger_id', $id)
        ->whereDate('created_at', $id)
        ->pluck('opening')->pop();
        // ->toSql();

        if (is_null($opening)) {
            $opening = 0;
        }

        return ['openingBalance' => $opening, 'vouchers' => $voucher];
    }

    public function create($voucher_data) {
        if ($voucher_data['cr'] == $voucher_data['dr']) {
            // If Creditor and Debtor are Same
            return response('CR and DR Same', 400);
        }
        $user_id = Auth::user()->id;
        $voucher = Voucher::create([
            'cr' => $voucher_data['cr'],
            'dr' => $voucher_data['dr'],
            'narration' => $voucher_data['narration'],
            'amount' => $voucher_data['amount'],
            'user_id' => $user_id
        ]);

        return $voucher;
    }

    public function update($voucher_data) {
        $voucher = Voucher::findOrFail($voucher_data['id']);
        $voucher->cr = $voucher_data['cr'];
        $voucher->dr = $voucher_data['dr'];
        $voucher->amount = $voucher_data['amount'];
        $voucher->narration = $voucher_data['narration'];
        $voucher->save();
    }

    public function __construct() {}
}
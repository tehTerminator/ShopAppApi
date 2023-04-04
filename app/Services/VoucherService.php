<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\Balance;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherService
{

    public static function select(int $id, $from, $to)
    {
        $voucher = NULL;
        if ($from === $to) {
            $voucher = Voucher::whereDate('created_at', $from);
        } else {
            $to = Carbon::createFromFormat('Y-m-d', $to)->addDay(1);
            $voucher = Voucher::whereBetween('created_at', [$from, $to]);
        }
        $data = $voucher
            ->where(function ($query) use ($id) {
                $query->where('cr', $id)
                    ->orWhere('dr', $id);
            })->with(['creditor', 'debtor'])
            ->orderBy('created_at', 'ASC')
            ->get();

        $opening = Balance::where('ledger_id', $id)
            ->whereDate('created_at', $from)
            ->pluck('opening')->pop();
        // ->toSql();

        if (is_null($opening)) {
            $opening = 0;
        }

        return ['openingBalance' => $opening, 'vouchers' => $data];
    }

    public static function create($voucher_data)
    {
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

    public static function update($voucher_data)
    {
        $voucher = Voucher::findOrFail($voucher_data['id']);

        if ($voucher->immutable) {
            throw new Exception('Voucher Immutable');
        }

        $voucher->cr = $voucher_data['cr'];
        $voucher->dr = $voucher_data['dr'];
        $voucher->amount = $voucher_data['amount'];
        $voucher->narration = $voucher_data['narration'];
        $voucher->save();

        return $voucher;
    }


    public static function dayBook(string $date)
    {
        return Voucher::select(
            DB::raw("
                TRUNCATE(sum(vouchers.amount), 2) as amount
            "),
            DB::raw("cr, dr")
        )->whereDate('created_at', $date)
            ->with(['creditor', 'debtor'])
            ->groupBy(['cr', 'dr'])->get();
    }

    public function __construct(){    }
}

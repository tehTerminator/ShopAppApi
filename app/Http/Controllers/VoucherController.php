<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    private $ledger = 0;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function select(Request $request) {
        $this->validate($request, [
            'id' => 'integer|min:1',
        ]);

        if ($request->has('id')) {
            return response()->json(Voucher::findOrFail($request->query('id')));
        }

        $this->validate($request, [
            'date' => 'required|date',
            'ledger' => 'required|integer|min:1'
        ]);

        $this->ledger = $request->query('ledger');

        $voucher = Voucher::whereDate('created_at', $request->query('date'))
        ->where('state', 1)
        ->where(function($query) {
            $query->where('cr', $this->ledger)
            ->orWhere('dr', $this->ledger);
        })->with(['creditor', 'debtor'])
        ->get();
        // ->toSql();

        // return response($voucher);

        return response()->json($voucher);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'cr' => 'required|integer',
            'dr' => 'required|integer',
            'narration' => 'string',
            'amount' => 'required|numeric',
        ]);

        if ($request->input('cr') == $request->input('dr')) {
            // If Creditor and Debtor are Same
            return response('CR and DR Same', 400);
        }

        // $user_id = Auth::user()->id;

        $user_id = 1; // For Testing Purpose Only Please Change in Production

        $voucher = Voucher::create([
            'cr' => $request->cr,
            'dr' => $request->dr,
            'narration' => $request->narration,
            'amount' => $request->amount,
            'user_id' => $user_id
        ]);

        return response()->json($voucher);
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer',
            'cr' => 'required|integer',
            'dr' => 'required|integer',
            'narration' => 'alpha',
            'amount' => 'required|numeric',
        ]);

        $voucher = Voucher::findOrFail($request->id);
        $voucher->cr = $request->cr;
        $voucher->dr = $request->dr;
        $voucher->amount = $request->amount;
        $voucher->narration = $request->narration;
        $voucher->save();
    }
}

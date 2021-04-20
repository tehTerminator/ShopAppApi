<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LedgerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(Request $request) {
        $this->validate($request, [
            'cr' => 'required|integer',
            'dr' => 'required|integer',
            'narration' => 'alpha',
            'amount' => 'required|numeric',
        ]);

        if ($request->input('cr') == $request->input('dr')) {
            // If Creditor and Debtor are Same
            return response('CR and DR Same', 400);
        }

        $user_id = Auth::user()->id;

        Voucher::create([
            'cr' => $request->cr,
            'dr' => $request->dr,
            'narration' => $request->narration,
            'amount' => $request->amount,
            'user_id' => $user_id
        ]);
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

    public function delete($id) {
        $voucher = Voucher::findOrFail($id);
        $voucher->state = false;
        $voucher->save();

        return response('Voucher Deleted Successfully');
    }
}

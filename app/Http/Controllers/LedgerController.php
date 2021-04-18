<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Ledger;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

    public function select() {
        $ledgers = Ledger::with(['balance' => function($query) {
            $query->whereDate('created_at', Carbon::now());
        }])->get();
        return response()->json($ledgers);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'title' => 'required|unique:ledgers|max:50|min:3|string',
            'group' => 'required|in:BANK,CASH,PAYABLE,RECEIVABLE,EXPENSE',
        ]);

        $ledger = Ledger::create([
            'title' => $request->title,
            'group' => $request->group
        ]);

        return response()->json($ledger);
    }

    public function updateTitle(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer',
            'title' => 'required|unique:ledgers|max:50|min:3|alpha',
        ]);

        $ledger = Ledger::findOrFail($request->input('id'));
        $ledger->title = $request->input('title');
        $ledger->save();

        return response('Success', 200);
    }

    public function updateBalance(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer',
            'opening' => 'numeric',
            'closing' => 'numeric',
            'date' => 'required|date',
        ]);

        $opening = $request->has('opening') ? $request->opening : 0;
        $closing = $request->has('closing') ? $request->closing : 0;

        $balance = Balance::whereDate('created_at', $request->date)
        ->where('ledger_id', $request->id)->first();

        if (empty($balance)) {
            Balance::create([
                'ledger_id' => $request->id,
                'opening' => $opening,
                'closing' => $closing
            ]);
        } else {
            $balance->opening = $opening;
            $balance->closing = $closing;
            $balance->save();
        }

        return response('Success');
    }
}

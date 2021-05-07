<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Ledger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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

    public function select(Request $request) {
        $ledgers = 0;
        if($request->hasAny('id')) {
            $ledgers = Ledger::where('id', $request->query('id'))
            ->with(['balance' => function($query) {
                $query
                ->whereDate('created_at', Carbon::now());
            }])
            ->first();
        } else {
            $ledgers = Cache::remember('ledgers', 3600, function(){
                return Ledger::with(
                    ['balance' => function($query) {
                        $query
                        ->whereDate('created_at', Carbon::now());
                    }
                ])
                ->get();
            });
        }
        return response()->json($ledgers);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'title' => 'required|unique:ledgers|max:50|min:3|string',
            'kind' => 'required|in:BANK,CASH,PAYABLES,RECEIVABLES,EXPENSE,INCOME',
        ]);

        $ledger = Ledger::create([
            'title' => $request->title,
            'kind' => $request->kind
        ]);

        return response()->json($ledger);
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer',
            'title' => 'required|unique:ledgers|max:50|min:3|alpha',
            'kind' => 'required|in:BANK,CASH,PAYABLES,RECEIVABLES,EXPENSE,INCOME',
        ]);

        $ledger = Ledger::findOrFail($request->input('id'));
        $ledger->title = $request->input('title');
        $ledger->kind = $request->input('kind');
        $ledger->save();

        return response('Success', 200);
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

        $ledger = Ledger::where('id', $id)->with(['balance' => function($query) {
            $query
            ->whereDate('created_at', Carbon::now());
        }])
        ->first();
        return response()->json($ledger);
    }
}

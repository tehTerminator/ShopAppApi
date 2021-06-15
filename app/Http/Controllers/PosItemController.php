<?php

namespace App\Http\Controllers;

use App\Models\PosItem;
use App\Models\PosTemplate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class PosItemController extends Controller
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
        $posItems = Cache::remember('posItems', 3600, function() {
            return PosItem::with(['pos_templates'])->get();
        });
        return response()->json($posItems);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'title' => 'required|unique:pos_items|string',
            'rate' => 'required|min:1|integer',
        ]);

        $posItem = PosItem::create([
            'title' => $request->input('title'),
            'rate' => $request->input('rate')
        ]);

        Cache::forget('posItems');

        return response()->json($posItem);
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1',
            'title' => 'required|unique:pos_items|string',
            'rate' => 'required|min:1|integer',
        ]);

        $posItem = PosItem::findOrFail($request->input('id'));
        $posItem->title = $request->input('title');
        $posItem->rate = $request->input('rate');
        $posItem->save();

        Cache::forget('posItems');

        return response()->json($posItem);
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1'
        ]);

        $posItem = PosItem::findOrFail($request->input('id'));
        PosTemplate::where('positem_id', $posItem->id)->delete();
        $posItem->delete();

        Cache::forget('posItems');

        return response()->json(['message'=>'PosItem Deleted Successfully']);
    }
}

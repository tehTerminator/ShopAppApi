<?php

namespace App\Http\Controllers;

use App\Models\PosItem;
use App\Models\PosTemplate;
use Exception;
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
        $posItems = PosItem::with(['pos_templates'])->get();
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

        return response()->json($posItem);
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1'
        ]);

        $posItem = PosItem::findOrFail($request->input('id'));
        $templates = PosTemplate::where('positem_id', $posItem->id)->delete();
        $posItem->delete();

        return response()->json(['message'=>'PosItem Deleted Successfully']);
    }
}

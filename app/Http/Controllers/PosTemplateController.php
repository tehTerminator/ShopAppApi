<?php

namespace App\Http\Controllers;

use App\Models\PosTemplate;
use Illuminate\Http\Request;

class PosTemplateController extends Controller
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
            'positem_id' => 'required|integer|min:1',
            'item_id' => 'required|min:1|integer',
            'kind' => 'required|in:PRODUCT,LEDGER',
            'rate' => 'required|min:1|numeric',
            'quantity' => 'required|integer|min:1'
        ]);
        $template = PosTemplate::create([
            'positem_id' => $request->input('positem_id'),
            'item_id' => $request->input('item_id'),
            'kind' => $request->input('kind'),
            'rate' => $request->input('rate'),
            'quantity' => $request->input('quantity'),
        ]);
        return response()->json($template);
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1',
            'positem_id' => 'required|integer|min:1',
            'item_id' => 'required|min:1|integer',
            'kind' => 'required|in:PRODUCT,LEDGER',
            'rate' => 'required|min:1|numeric',
            'quantity' => 'required|numeric|min:1'
        ]);

        $template = PosTemplate::findOrFail($request->input('id'));
        $template->positem_id = $request->input('positem_id');
        $template->item_id = $request->input('item_id');
        $template->kind = $request->input('kind');
        $template->rate = $request->input('rate');
        $template->quantity = $request->input('quantity');
        $template->save();

        return response()->json($template);
        
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1'
        ]);

        PosTemplate::findOrFail($request->input('id'))->delete();
        return response()->json(['message'=>'Template Deleted Successfully']);
    }
}

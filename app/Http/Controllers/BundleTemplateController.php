<?php

namespace App\Http\Controllers;

use App\Models\BundleTemplate;
use Illuminate\Http\Request;

class BundleTemplateController extends Controller
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
            'bundle_id' => 'required|integer|min:1',
            'item_id' => 'required|min:1|integer',
            'kind' => 'required|in:PRODUCT,LEDGER',
            'rate' => 'required|min:1|numeric',
            'quantity' => 'required|integer|min:1'
        ]);
        $template = BundleTemplate::create([
            'bundle_id' => $request->input('bundle_id'),
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
            'bundle_id' => 'required|integer|min:1',
            'item_id' => 'required|min:1|integer',
            'kind' => 'required|in:PRODUCT,LEDGER',
            'rate' => 'required|min:1|numeric',
            'quantity' => 'required|numeric|min:1'
        ]);

        $template = BundleTemplate::findOrFail($request->input('id'));
        $template->bundle_id = $request->input('bundle_id');
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

        BundleTemplate::findOrFail($request->input('id'))->delete();
        return response()->json(['message'=>'Template Deleted Successfully']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BundleTemplate;
<<<<<<< HEAD:app/Http/Controllers/BundleTemplateController.php
=======
use App\Services\BundleService;
>>>>>>> 205dbe79be83201febf27f0369d3b07ce586ba7d:app/Http/Controllers/PosTemplateController.php
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
            'id' => [
                'exists:App\Models\BundleTemplate,id', 'required', 
                'integer', 
                'min:1'
            ],
            'bundle_id' => [
                'exists:App\Models\Bundle,id', 
                'required', 
                'integer', 
                'min:1'
            ],
            'item_id' => ['required', 'min:1', 'integer'],
            'kind' => ['required', 'in:PRODUCT,LEDGER'],
            'rate' => ['required', 'min:1', 'numeric'],
            'quantity' => ['required', 'numeric', 'min:1']
        ]);

        return response()->json(
            BundleService::updateTemplate(
                $request->bundle_id,
                $request->id,
                $request->item_id,
                $request->kind,
                $request->rate,
                $request->quantity
            )
        ); 
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1'
        ]);

        BundleTemplate::findOrFail($request->input('id'))->delete();
        return response()->json(['message'=>'Template Deleted Successfully']);
    }
}

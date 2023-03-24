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
<<<<<<< HEAD:app/Http/Controllers/BundleTemplateController.php
        $template = BundleTemplate::create([
            'bundle_id' => $request->input('bundle_id'),
            'item_id' => $request->input('item_id'),
            'kind' => $request->input('kind'),
            'rate' => $request->input('rate'),
            'quantity' => $request->input('quantity'),
        ]);
        return response()->json($template);
=======

        return response()->json(
            BundleService::createTemplate(
                $request->bundle_id,
                $request->item_id,
                $request->kind,
                $request->rate,
                $request->quantity
            )
        );
>>>>>>> 205dbe79be83201febf27f0369d3b07ce586ba7d:app/Http/Controllers/PosTemplateController.php
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
<<<<<<< HEAD:app/Http/Controllers/BundleTemplateController.php
        $template->bundle_id = $request->input('bundle_id');
=======
        $template->positem_id = $request->input('positem_id');
>>>>>>> 205dbe79be83201febf27f0369d3b07ce586ba7d:app/Http/Controllers/PosTemplateController.php
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

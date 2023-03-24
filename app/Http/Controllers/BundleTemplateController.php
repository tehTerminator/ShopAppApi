<?php

namespace App\Http\Controllers;

use App\Models\BundleTemplate;
use Illuminate\Http\Request;
use App\Services\BundleService;

class BundleTemplateController extends Controller
{
    public function create(Request $request) {
        $this->validate($request, [
            'bundle_id' => 'required|integer|min:1',
            'item_id' => 'required|min:1|integer',
            'kind' => 'required|in:PRODUCT,LEDGER',
            'rate' => 'required|min:1|numeric',
            'quantity' => 'required|integer|min:1'
        ]);

        return response()->json(
            BundleService::createTemplate(
                $request->bundle_id,
                $request->item_id,
                $request->kind,
                $request->rate,
                $request->quantity
            )
        );
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
            'id' => [
                'exists:App\Models\BundleTemplate,id',
                'required',
                'integer',
                'min:1'
            ]
        ]);
        BundleService::deleteTemplate($request->id);
    }

     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}
}

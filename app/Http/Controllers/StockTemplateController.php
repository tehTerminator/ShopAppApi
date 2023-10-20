<?php

namespace App\Http\Controllers;

use App\Constants\HttpCodes;
use App\Models\StockTemplate;
use App\Services\ValidationService;
use Illuminate\Http\Request;

class StockTemplateController extends Controller
{

    public function create(Request $request)
    {
        $this->validateRequest($request);
        return response()->json(
            StockTemplate::create(
                $request->only([
                    'product_id',
                    'stock_item_id',
                    'quantity'
                ])
            )
        );
    }

    public function update(Request $request) {
        $this->validateRequest($request, 'update');
        $template = StockTemplate::findOrFail($request->id);
        $template->update(
            $request->only(['product_id', 'stock_item_id', 'quantity'])
        );
        return response()->json($template);
    }

    public function delete(Request $request) {
        $this->validate($request, ['id' => 'required']);
        StockTemplate::findOrFail($request->id)->delete();
        return response('Template Deleted Success', 200);
    }

    private function validateRequest(Request $request, string $requestType = 'create')
    {
        $validated = ValidationService::validateModel($request, 'stockTemplate', $requestType);

        if (!$validated) {
            return response('Invalid Data Received', HttpCodes::UNPROCESSABLE_ENTITY['code']);
        }

        return true;
    }

    public function __construct()
    {
    }
}

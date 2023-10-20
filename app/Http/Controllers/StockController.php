<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Services\ValidationService;
use Illuminate\Http\Request;
use App\Constants\HttpCodes;

class StockController extends Controller
{

    public function create(Request $request)
    {
        $this->validateRequest($request);
        return response()->json(
            Stock::create(
                $request->only(['title', 'quantity'])
            )
        );
    }

    private function validateRequest(Request $request, string $requestType = 'create')
    {
        $validated = ValidationService::validateModel($request, 'stocks', $requestType);

        if (!$validated) {
            return response('Invalid Data Received', HttpCodes::UNPROCESSABLE_ENTITY['code']);
        }

        return true;
    }

    public function __construct()
    {
    }
}

<?php 

namespace App\Http\Controllers;

use App\Models\StockUsage;
use Illuminate\Http\Request;
use App\Services\ValidationService;
use Illuminate\Support\Facades\Auth;

class StockUsageController extends Controller {

    public function create(Request $request) {
        $this->validateRequest($request);
        
    }



    private function validateRequest(Request $request, $requestType = 'create') {
        $validated = ValidationService::validateModel($request, 'stockUsage', $requestType);

        if (!$validated) {
            return response('Invalid Data Received', 201);
        }

        return;
    }

    public function __construct() {}
}
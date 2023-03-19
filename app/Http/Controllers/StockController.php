<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StockService;

class StockController extends Controller {

    public function __construct() {}

    public function create(Request $req) {
        $this->validate($req, StockService::getValidationRules());
        return response()->json(StockService::create($req->title, $req->quantity));
    }

    public function update(Request $req) {
        $this->validate($req, StockService::getValidationRules(false));
        return response()->josn(StockService::update($req->id, $req->title));
    }

    public function delete(Request $req) {
        StockService::delete($req->id);
    }

    public function demo() {
        return StockService::getValidationRules();
    }
}
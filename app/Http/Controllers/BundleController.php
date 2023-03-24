<?php

namespace App\Http\Controllers;

use App\Services\BundleService;
use Illuminate\Http\Request;

class BundleController extends Controller
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
        BundleService::selectBundle();
    }

    public function create(Request $request) {
        $this->validate($request, [
            'title' => 'required|unique:App\Models\Bundle,title|string',
            'rate' => 'required|min:1|integer',
        ]);

        return response()->json(
            BundleService::createBundle($request->title, $request->rate)
        );
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1',
            'title' => 'required|unique:App\Models\Bundle|string',
            'rate' => 'required|min:1|integer',
        ]);

        return response()->json(
            BundleService::updateBundle(
                $request->id,
                $request->title,
                $request->rate
            )
        );
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1'
        ]);
        BundleService::deleteBundle($request->id);
    }
}

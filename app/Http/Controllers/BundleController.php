<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use App\Models\BundleTemplate;
use Illuminate\Support\Facades\Cache;
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
        $bundles = Cache::remember('bundles', 6000, function() {
            return Bundle::with('bundle_template')->get();
        });
        return response()->json($bundles);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'title' => 'required|unique:pos_items|string',
            'rate' => 'required|min:1|integer',
        ]);

        $bundle = Bundle::create([
            'title' => $request->input('title'),
            'rate' => $request->input('rate')
        ]);

        Cache::forget('bundles');

        return response()->json($bundle);
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1',
            'title' => 'required|unique:pos_items|string',
            'rate' => 'required|min:1|integer',
        ]);

        $bundle = Bundle::findOrFail($request->input('id'));
        $bundle->title = $request->input('title');
        $bundle->rate = $request->input('rate');
        $bundle->save();

        Cache::forget('bundles');

        return response()->json($bundle);
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1'
        ]);

        $bundle = Bundle::findOrFail($request->input('id'));
        Bundle::where('positem_id', $bundle->id)->delete();
        $bundle->delete();

        Cache::forget('bundles');

        return response()->json(['message'=>'Bundle Deleted Successfully']);
    }
}

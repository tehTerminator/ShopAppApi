<?php

namespace App\Http\Controllers;

<<<<<<< HEAD:app/Http/Controllers/BundleController.php
use App\Models\Bundle;
use App\Models\BundleTemplate;
use Illuminate\Support\Facades\Cache;
=======
use App\Services\BundleService;
>>>>>>> 205dbe79be83201febf27f0369d3b07ce586ba7d:app/Http/Controllers/PosItemController.php
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
<<<<<<< HEAD:app/Http/Controllers/BundleController.php
        $bundles = Cache::remember('bundles', 6000, function() {
            return Bundle::with('bundle_template')->get();
        });
        return response()->json($bundles);
=======
        BundleService::selectBundle();
>>>>>>> 205dbe79be83201febf27f0369d3b07ce586ba7d:app/Http/Controllers/PosItemController.php
    }

    public function create(Request $request) {
        $this->validate($request, [
            'title' => 'required|unique:App\Models\Bundle,title|string',
            'rate' => 'required|min:1|integer',
        ]);

<<<<<<< HEAD:app/Http/Controllers/BundleController.php
        $bundle = Bundle::create([
            'title' => $request->input('title'),
            'rate' => $request->input('rate')
        ]);

        Cache::forget('bundles');

        return response()->json($bundle);
=======
        return response()->json(
            BundleService::createBundle($request->title, $request->rate)
        );
>>>>>>> 205dbe79be83201febf27f0369d3b07ce586ba7d:app/Http/Controllers/PosItemController.php
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1',
            'title' => 'required|unique:App\Models\Bundle|string',
            'rate' => 'required|min:1|integer',
        ]);

<<<<<<< HEAD:app/Http/Controllers/BundleController.php
        $bundle = Bundle::findOrFail($request->input('id'));
        $bundle->title = $request->input('title');
        $bundle->rate = $request->input('rate');
        $bundle->save();

        Cache::forget('bundles');

        return response()->json($bundle);
=======
        return response()->json(
            BundleService::updateBundle(
                $request->id,
                $request->title,
                $request->rate
            )
        );
>>>>>>> 205dbe79be83201febf27f0369d3b07ce586ba7d:app/Http/Controllers/PosItemController.php
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1'
        ]);
<<<<<<< HEAD:app/Http/Controllers/BundleController.php

        $bundle = Bundle::findOrFail($request->input('id'));
        Bundle::where('positem_id', $bundle->id)->delete();
        $bundle->delete();

        Cache::forget('bundles');

        return response()->json(['message'=>'Bundle Deleted Successfully']);
=======
        BundleService::deleteBundle($request->id);
>>>>>>> 205dbe79be83201febf27f0369d3b07ce586ba7d:app/Http/Controllers/PosItemController.php
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
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
            'title' => 'required|unique:customers|string',
            'address' => 'required|string'
        ]);

        $customer = Customer::create([
            'title' => $request->input('title'),
            'address' => $request->input('address')
        ]);

        return response()->json($customer);
    }

    public function update(Request $request) {
        $this->validate($request, [
            'title' => 'required|unique:customers|string',
            'address' => 'required|string'
        ]);

        $customer = Customer::findOrFail($request->input('id'));
        $customer->title = $request->input('title');
        $customer->address = $request->input('address');
        $customer->save();

        return response()->json($customer);
    }
}

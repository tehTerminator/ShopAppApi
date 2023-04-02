<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerService;
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

    public function select() {
        return Customer::all();
    }

    public function create(Request $request) {
        $this->validate($request, [
            'title' => 'required|unique:customers|string',
            'address' => 'required|string',
            'mobile' => 'regex:/^[6-9][0-9]{9}$/',
            'ledger_id' => 'numeric|exists:App\Models\Ledger,id'
        ]);

        $defaultCustomerId = CustomerService::getDefaultCustomer()->ledger_id;

        $customer = Customer::create([
            'title' => $request->input('title'),
            'address' => $request->input('address'),
            'mobile' => $request->input('mobile'),
            'ledger_id' => $request->input('ledger_id', $defaultCustomerId)
        ]);

        return response()->json($customer);
    }

    public function update(Request $request) {
        $this->validate($request, [
            'title' => 'required|string',
            'address' => 'required|string',
            'mobile' => 'regex:/^[6-9][0-9]{9}$/'
        ]);
        $customer = Customer::findOrFail($request->input('id'));

        try{
            $customer->title = $request->input('title');
            $customer->address = $request->input('address');
            $customer->save();
        } catch (\Exception $ex) {
            return response('Unable to Update Record', 500);
        }

        return response()->json($customer);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerService;
use App\Services\ValidationService;
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

    public function select()
    {
        return Customer::all();
    }

    public function create(Request $request)
    {
        $this->validateRequest($request);
        $defaultCustomerId = CustomerService::getDefaultCustomer()->ledger_id;
        $customer = Customer::create(
            [
                'title' => $request->input('title'),
                'address' => $request->input('address'),
                'mobile' => $request->input('mobile'),
                'ledger_id' => $request->input('ledger_id', $defaultCustomerId)
            ]
        );

        return response()->json($customer);
    }

    public function update(Request $request)
    {
        $this->validateRequest($request, 'update');
        $customer = Customer::findOrFail($request->input('id'));

        try {
            $customer->title = $request->input('title');
            $customer->address = $request->input('address');
            $customer->save();
        } catch (\Exception $ex) {
            return response('Unable to Update Record', 500);
        }

        return response()->json($customer);
    }

    private function validateRequest(Request $request, string $requestType = 'create')
    {
        $validated = ValidationService::validateModel($request, 'customer', $requestType);

        if (!$validated) {
            return response('Invalid Data Received', 201);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
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
            'customer_id' => 'required|integer|min:1',
            'paymentMethod' => 'required|alpha',
            'amount' => 'required|numeric|min:1'
        ]);

        $invoice = Invoice::create([
            'title' => $request->input('title'),
            'address' => $request->input('address')
        ]);

        return response()->json($invoice);
    }

    public function update(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer|min:1',
            'customer_id' => 'required|integer|min:1',
            'paymentMethod' => 'required|alpha',
            'amount' => 'required|numeric|min:1'
        ]);

        $invoice = Invoice::findOrFail($request->input('id'));
        $invoice->id = $request->input('id');
        $invoice->customer_id = $request->input('customer_id');
        $invoice->amount = $request->input('amount');
        $invoice->save();

        return response()->json($invoice);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    private $service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->service = new InvoiceService();
    }

    public function select(Request $request) {
       $data = $this->service->getInvoices($request);
       return response()->json($data);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'customer_id' => 'required|integer|min:1',
            'paymentMethod' => 'required',
            'amount' => 'required|numeric|min:1',
        ]);

        $this->validate($request, [
            'transactions.*.item_id' => 'required|integer|min:1',
            'transactions.*.item_type' => 'required|in:LEDGER,PRODUCT',
            'transaction.*.description' => 'string',
            'transaction.*.quantity' => 'required|numberic|min:1',
            'transaction.*.rate' => 'required|numeric|min:1',
            'transaction.*.discount' => 'required|numeric|min:0|max:100'
        ]);
        
        $user_id = Auth::user()->id;
        
        $this->service->createNewInvoice($request, $user_id);
    }

    public function delete(int $id) {
        $this->service->delete($id);
        return response()->json(['message' => 'Invoice #' . $id . 'Deleted Successfully']);
    }
}

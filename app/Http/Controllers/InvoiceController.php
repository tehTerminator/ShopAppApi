<?php

namespace App\Http\Controllers;

use App\Models\PaymentInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\InvoiceService;
use App\Models\Voucher;

class InvoiceController extends Controller
{
    private $service;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    public function select(Request $request) {
       $data = InvoiceService::getInvoices($request);
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
        
        $response = InvoiceService::createNewInvoice($request, $user_id);
        return response()->json($response);
        
    }

    public function delete(int $id) {
        InvoiceService::delete($id);
        return response()->json(['message' => 'Invoice #' . $id . 'Deleted Successfully']);
    }

    public function getPaymentInfo(Request $request) {
        $this->validate($request, [
            'id' => 'required|numeric|exists:App\Models\Invoice,id'
        ]);
        $voucher_id = PaymentInfo::where('invoice_id', $request->id)->get()->pluck('voucher_id')->toArray();

        $vouchers = Voucher::whereIn('id', $voucher_id)->with(['creditor', 'debtor'])->get();

        return response()->json($vouchers);
    }
}

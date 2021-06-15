<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\Ledger;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function select(Request $request) {
        if ($request->has('id')) {
            $invoice = Invoice::where('id', $request->query('id'))->with(['customer', 'transactions'])->first();
            return response()->json($invoice);
        }

        if ($request->has('createdAt')) {
            $invoices = Invoice::whereDate('created_at', $request->query('createdAt'))
            ->where('user_id', $request->query('userId'))
            ->with(['customer'])->get();
            return response()->json($invoices);
        }

        return response()->json(['status'=>'Please Either Provide Date or Id']);
    }

    public function create(Request $request) {
        $this->validate($request, [
            'customer_id' => 'required|integer|min:1',
            'paymentMethod' => 'required',
            'amount' => 'required|numeric|min:1',
        ]);
        
        $user_id = Auth::user()->id;
        
        $invoice = Invoice::create([
            'customer_id' => $request->input('customer_id'),
            'user_id' => $user_id,
            'paid' => $request->input('paid'),
            'paymentMethod' => $request->input('paymentMethod'),
            'amount' => $request->input('amount'),
        ]);

        $this->createTransactions($request, $invoice->id);
        $this->createPaymentVoucher($request->get('transactions'), $invoice->id);
        $this->createReceiptVoucher($invoice->paymentMethod, $invoice->id, $invoice->amount);
    }

    private function createTransactions(Request $request, $invoice_id) {
        $this->validate($request, [
            'transactions.*.item_id' => 'required|integer|min:1',
            'transactions.*.item_type' => 'required|in:LEDGER,PRODUCT',
            'transaction.*.description' => 'string',
            'transaction.*.quantity' => 'required|numberic|min:1',
            'transaction.*.rate' => 'required|numeric|min:1',
            'transaction.*.discount' => 'required|numeric|min:0|max:100'
        ]);

        $user_id = Auth::user()->id;

        $transactions = $request->get('transactions');
        for($i=0; $i < count($transactions); $i++) {
            $transactions[$i]['invoice_id'] = $invoice_id;
            $transactions[$i]['user_id'] = $user_id;
            Transaction::create($transactions[$i]);
        }
    }

    private function createPaymentVoucher($transactions, $invoice_id) {
        $customer = Ledger::where('title', 'Customer')->where('kind', 'RECEIVABLES')->first();
        if (!$customer) {
            $customer = Ledger::create([
                'title' => 'Customer',
                'kind' => 'RECEIVABLES'
            ]);
        }

        $sales = Ledger::where('title', 'Sales')->first();
        if (!$sales) {
            $sales = Ledger::create([
                'title' => 'Sales',
                'kind' => 'INCOME'
            ]);
        }

        $saleAmount = 0;
        $user_id = Auth::user()->id;

        for ($i = 0; $i < count($transactions); $i++) {
            if ($transactions[$i]['item_type'] === 'LEDGER') {
                Voucher::create([
                    'cr' => $transactions[$i]['item_id'],
                    'dr' => $customer->id,
                    'narration' => 'Payment Invoice #' . $invoice_id,
                    'amount' => $this->getAmount($transactions[$i]),
                    'user_id' => $user_id
                ]);
            } else {
                $saleAmount += $this->getAmount($transactions[$i]);
            }
        }

        if ($saleAmount > 0) {
            Voucher::create([
                'cr' => $sales->id,
                'dr' => $customer->id,
                'narration' => 'Sale Invoice #' . $invoice_id,
                'amount' => $saleAmount,
                'user_id' => $user_id
            ]);
        }
    }

    private function createReceiptVoucher($paymentMethod, $invoice_id, $amount) {
        $user_id = Auth::user()->id;
        $customer = Ledger::where('title', 'Customer')->where('kind', 'RECEIVABLES')->first();
        if (!$customer) {
            $customer = Ledger::create([
                'title' => 'Customer',
                'kind' => 'RECEIVABLES'
            ]);
        }

        if ($paymentMethod <> 'UDHAAR') {
            $receiver_id = explode('#', $paymentMethod)[1];
            Voucher::create([
                'cr' => $customer->id,
                'dr' => $receiver_id,
                'narration' => 'Payment Receipt for Invoice #' . $invoice_id,
                'amount' => $amount,
                'user_id' => $user_id
            ]);
        }

        return response()->json(['status' => 'Success']);
    }

    private function getAmount($transaction) {
        return ($transaction['quantity'] * $transaction['rate']) * (1 - $transaction['discount']/100);
    }
}

<?php 

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\Ledger;
use App\Models\Transaction;
use App\Models\Voucher;

class InvoiceService {

    public function getInvoiceById(int $id) {
        $invoice = Invoice::where('id', $id)
        ->with(['customer', 'transactions'])
        ->first();
        return $invoice;
    }

    public function getInvoiceByDate(int $user, $created_at) {
        $invoices = Invoice::whereDate('created_at', $created_at)
            ->where('user_id', $user)
            ->with(['customer'])->get();

        return $invoices;
    }

    public function createNewInvoice(Request $request, int $user_id) {
        $invoice = Invoice::create([
            'customer_id' => $request->input('customer_id'),
            'user_id' => $user_id,
            'paid' => $request->boolean('paid'),
            'paymentMethod' => $request->input('paymentMethod'),
            'amount' => $request->input('amount'),
        ]);
        $this->createTransactions(
            $request->get('transactions'), 
            $invoice->id, 
            $user_id
        );
        $this->createPaymentVoucher(
            $request->get('transactions'), 
            $invoice->id, 
            $user_id
        );
        $this->createReceiptVoucher(
            $invoice->paymentMethod, 
            $invoice->id,
            $invoice->amount, 
            $user_id
        );
    }

    private function createTransactions(
        array $transactions, 
        int $invoice_id, 
        int $user_id
    ) 
    {
        for($i=0; $i < count($transactions); $i++) {
            $transactions[$i]['invoice_id'] = $invoice_id;
            $transactions[$i]['user_id'] = $user_id;
            Transaction::create($transactions[$i]);
        }
    }

    private function createPaymentVoucher(
        array $transactions, 
        $invoice_id, 
        $user_id
    ) 
    {
        $customer = Ledger::where('title', 'Customer')
        ->where('kind', 'RECEIVABLES')->first();
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

    private function createReceiptVoucher(
        string $paymentMethod, 
        int $invoice_id, 
        float $amount, 
        int $user_id
    ) 
    {
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
                'narration' => 'Receipt Invoice #' . $invoice_id,
                'amount' => $amount,
                'user_id' => $user_id
            ]);
        }

        return response()->json(['status' => 'Success']);
    }

    private function getAmount($transaction) {
        return 
            ($transaction['quantity'] * $transaction['rate']) 
            * (1 - $transaction['discount']/100);
    }

    public function __construct(){}
}
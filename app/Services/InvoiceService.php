<?php 

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\Ledger;
use App\Models\Transaction;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class InvoiceService {

    public static function select(Request $request) {
        if ($request->has('id')) {
            return self::getInvoiceById($request->id);
        }

        else if ($request->has('createdAt')) {
            return self::getInvoiceByDate($request->userId, $request->createdAt);
        }

        else if ($request->has('customerId')) {
            return self::getInvoiceByCustomer($request->customerId, $request->month, $request->input('paid', false));
        }

        return response('Invalid Query Parameter', 406);
    }

    public static function getInvoiceById(int $id) {
        $invoice = Invoice::where('id', $id)
        ->with(['customer', 'generalTransactions'])
        ->first();
        return $invoice;
    }

    public static function getInvoiceByDate(int $user, $created_at) {
        $invoices = Invoice::whereDate('created_at', $created_at)
            ->where('user_id', $user)
            ->with(['customer'])->get();

        return $invoices;
    }

    public static function getInvoiceByCustomer(int $customer_id, string $created_at, bool $paid) {
        $invoices = Invoice::where('created_at', 'LIKE', $created_at . '%')
        ->where('paid', $paid);
        
        if ($customer_id > 0) {
            $invoices = $invoices->where('customer_id', $customer_id);
        }
        $invoices = $invoices->with(['customer'])->get();
        return $invoices;
    }

    public static function createNewInvoice(Request $request, int $user_id) {
        DB::beginTransaction();
        
        try{
            DB::commit();
            $invoice = Invoice::create([
                'customer_id' => $request->input('customer_id'),
                'user_id' => $user_id,
                'paid' => $request->boolean('paid'),
                'paymentMethod' => $request->input('paymentMethod'),
                'amount' => $request->input('amount'),
            ]);
        } catch(\Exception $e) {
            DB::rollBack();

            return response('Error', 500);
        }

        
        self::createTransactions(
            $request->get('transactions'), 
            $invoice->id, 
            $user_id
        );
        self::createPaymentVoucher(
            $request->get('transactions'), 
            $invoice->id, 
            $user_id
        );
        self::createReceiptVoucher(
            $invoice->paymentMethod, 
            $invoice->id,
            $invoice->amount, 
            $user_id
        );
    }

    private static function createTransactions(
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

    private static function createPaymentVoucher(
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
                    'amount' => self::getAmount($transactions[$i]),
                    'user_id' => $user_id
                ]);
            } else {
                $saleAmount += self::getAmount($transactions[$i]);
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

    private static function createReceiptVoucher(
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

    public static function delete(int $invoice_id) {
        Transaction::where('invoice_id', $invoice_id)->delete();
        Invoice::find($invoice_id)->delete();
        Voucher::where('narration', 'LIKE', '%' . $invoice_id)->delete();
    }

    private static function getAmount($transaction) {
        return 
            ($transaction['quantity'] * $transaction['rate']) 
            * (1 - $transaction['discount']/100);
    }

    public function __construct(){}
}
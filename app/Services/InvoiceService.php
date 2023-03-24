<?php 

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\Ledger;
use App\Models\Voucher;
use App\Models\GeneralTransactions;
use App\Models\DetailedTransactions;
use App\Models\StockTransaction;
use App\Models\Contact;
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
            
            $invoice = Invoice::create([
                'customer_id' => $request->input('customer_id'),
                'user_id' => $user_id,
                'paid' => $request->boolean('paid'),
                'paymentMethod' => $request->input('paymentMethod'),
                'amount' => $request->input('amount'),
            ]);

            self::createTransactions(
                $invoice->id,
                $request->input('generalTransactions', []),
                $request->input('detailedTransactions', []),
                $request->input('stockTransactions', [])
            );

            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();

            return response('Error', 500);
        }

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

    public function createTransactions(
        int $invoice_id, 
        array $generalTransactions, 
        array $detailedTransactions, 
        array $stockTransactions) {

        for($i=0; $i < count($generalTransactions); $i++) {
            $generalTransactions[$i]['invoice_id'] = $invoice_id;
        }

        for($i = 0; $i < count($detailedTransactions); $i++) {
            $detailedTransactions[$i]['invoice_id'] = $invoice_id;
        }

        for($i = 0; $i < count($stockTransactions); $i++) {
            $stockTransactions[$i]['invoice_id'] = $invoice_id;
        }

        GeneralTransactions::insert($generalTransactions);
        DetailedTransactions::insert($detailedTransactions);
        StockTransaction::insert($stockTransactions);
    }

    private static function getCustomerLedger($customer_id) {
        $customer = Contact::findOrFail($customer_id);

        if (!is_null($customer->ledger_id)) {
            return $customer->ledger_id;
        }

        $ledger_id = Contact::where('title', 'Walk-in Customer')->first()->ledger_id;
        if (is_null($ledger_id)) {
            $ledger = Ledger::create(['title' => 'Walk-in Customer','kind' => 'RECEIVABLES']);
            Contact::create(['title' => 'Walk-in Customer','address' => 'Ashoknagar', 'ledger_id' => $ledger->id]);
            return $ledger->id;
        }

        return $ledger_id;
    }

    private static function getSalesLedgerId() {
        $sales = Ledger::where('title', 'Sales')->first();
        if (!$sales) {
            $sales = Ledger::create([
                'title' => 'Sales',
                'kind' => 'INCOME'
            ]);
        }

        return $sales->id;

    }

    private static function createPaymentVoucher(
        array $transactions,
        int $customer_id,
        int $invoice_id, 
        int $user_id
    ) 
    {
        $customer_ledger = self::getCustomerLedger($customer_id);
        $sales_ledger_id = self::getSalesLedgerId();
        
        $saleAmount = 0;

        for ($i = 0; $i < count($transactions); $i++) {
            if ($transactions[$i]['item_type'] === 'LEDGER') {
                Voucher::create([
                    'cr' => $transactions[$i]['item_id'],
                    'dr' => $customer_ledger,
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
                'cr' => $sales_ledger_id,
                'dr' => $customer_ledger,
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
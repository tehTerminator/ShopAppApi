<?php 

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\Ledger;
use App\Models\Transaction;
use App\Models\Voucher;
use App\Models\PaymentInfo;
use App\Services\CustomerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceService {


    public static function getInvoices(Request $request) {
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
        ->with(['customer', 'transactions'])
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
        // return $request->all();

        
        DB::beginTransaction();

        try{

            $invoice = Invoice::create([
                'customer_id' => $request->input('customer_id'),
                'user_id' => $user_id,
                'paid' => $request->boolean('paid'),
                'amount' => $request->input('amount'),
            ]);
            self::createTransactions(
                $request->input('transactions'),
                $invoice->id, 
                $user_id
            );
            self::createPaymentVoucher(
                $request->input('transactions'),
                $invoice->id, 
                $invoice->customer_id,
            );
            self::createReceiptVoucher(
                $invoice->id,
                $invoice->customer_id,
                $invoice->paymentMethod, 
                $invoice->amount
            );

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return ['error' => $ex];
        }

        return ['status' => 'success'];
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
            unset($transactions[$i]['created_at']);
            unset($transactions[$i]['updated_at']);   
        }
        Transaction::insert($transactions);
    }

    private static function createPaymentVoucher(
        array $transactions, 
        int $invoice_id, 
        int $customer_id
    )
    {
        $customer = CustomerService::getCustomerLedger($customer_id);

        $sales = Ledger::where('title', 'Sales')->first();
        if (!$sales) {
            $sales = Ledger::create([
                'title' => 'Sales',
                'kind' => 'INCOME'
            ]);
        }

        $saleAmount = 0;
        $vouchers = [];

        for ($i = 0; $i < count($transactions); $i++) {
            if ($transactions[$i]['item_type'] == 'LEDGER') {
                array_push($vouchers, [
                    'cr' => $transactions[$i]['item_id'],
                    'dr' => $customer->id,
                    'narration' => 'Payment Invoice #' . $invoice_id,
                    'amount' => self::getAmount($transactions[$i]),
                    'user_id' => Auth::user()->id
                ]);
            } else {
                $saleAmount += self::getAmount($transactions[$i]);
            }
        }

        if ($saleAmount > 0) {
            array_push($vouchers, [
                'cr' => $sales->id,
                'dr' => $customer->id,
                'narration' => 'Sale Invoice #' . $invoice_id,
                'amount' => $saleAmount,
                'user_id' => Auth::user()->id
            ]);
        }

        Voucher::insert($vouchers);
    }

    private static function createReceiptVoucher(
        int $invoice_id, 
        int $customer_id,
        int $paymentMethod, 
        $amount
    )
    {

        if(is_null($paymentMethod)) {
            return response()->json(['status' => 'Udhaar Payment Created']);
        }

        $customer_ledger = CustomerService::getCustomerLedger($customer_id);
        $voucher = Voucher::create([
            'cr' => $customer_ledger,
            'dr' => $paymentMethod,
            'narration' => 'Receipt Invoice #' . $invoice_id,
            'amount' => $amount,
            'user_id' => Auth::user()->id
        ]);

        self::createPaymentInfo($invoice_id, $voucher->id, $amount);

        return response()->json(['status' => 'Success']);
    }

    public static function createPaymentInfo(int $invoice_id, int $voucher_id, float $amount) {
        return PaymentInfo::create([
            'invoice_id' => $invoice_id,
            'user_id' => Auth::user()->id,
            'voucher_id' => $voucher_id,
            'amount' => $amount
        ]);
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
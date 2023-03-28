<?php

use App\Models\Ledger;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->get('username', ['uses' => 'UserController@selectUsername']);
$router->get('displayName', ['uses' => 'UserController@selectDisplayName']);


$router->group(['middleware'=>'auth'], function() use ($router) {
    $router->get('dailyStats', ['uses' => 'HomeController@dailyStats']);
    $router->get('monthlyStats', ['uses' => 'HomeController@monthlyStats']);
    $router->get('userWiseInvoiceCount', ['uses' => 'UserReportController@userWiseInvoiceCount']);
    $router->get('userWisePaymentCount', ['uses' => 'UserReportController@userWisePaymentCount']);
    $router->get('userWiseSalesCount', ['uses' => 'UserReportController@userWiseSalesCount']);
    $router->get('productWiseSaleCount', ['uses' => 'HomeController@productWiseSaleCount']);
    $router->get('incomeExpense', ['uses' => 'HomeController@incomeExpense']);
    $router->get('operatorPerformance', ['uses' => 'HomeController@operatorPerformance']);
    $router->get('balance', ['uses' => 'LedgerController@selectBalance']);
    $router->put('balance/create', ['uses' => 'LedgerController@updateBalance']);
    $router->post('balance/update', ['uses'=>'LedgerController@takeBalanceSnapshot']);

    $router->get('day-book', function(Request $request) {
        $dayBook = DB::select('call tallyEntries(?)', [$request->query('date')]);
        return response()->json($dayBook);
    });
});

$router->group(['prefix'=>'users'], function() use ($router) {
    $router->post('login', ['uses' => 'UserController@login']);
    $router->put('create', ['uses' => 'UserController@register']);
});

$router->group(['prefix'=>'ledgers', 'middleware'=>'auth'], function() use ($router) {
    $router->get('', ['uses' => 'LedgerController@select']);
    $router->put('create', ['uses' => 'LedgerController@create']);
    $router->post('update', ['uses' => 'LedgerController@update']);
});

$router->group(['prefix'=>'products', 'middleware'=>'auth'], function() use ($router) {
    $router->put('create', ['uses' => 'ProductController@create']);
    $router->post('update', ['uses' => 'ProductController@update']);
    $router->delete('delete/{id}', ['uses' => 'ProductConroller@delete']);
});

// $router->group(['prefix'=>'pos-items', 'middleware'=>'auth'], function() use ($router) {
//     $router->get('', ['uses' => 'PosItemController@select']);
//     $router->put('create', ['uses' => 'PosItemController@create']);
//     $router->post('update', ['uses' => 'PosItemController@update']);
//     $router->delete('delete/{id}', function($id) {
//         $posItem = PosItem::findOrFail($id);
//         PosTemplate::where('positem_id', $posItem->id)->delete();
//         $posItem->delete();

//         return response()->json(['message'=>'PosItem Deleted Successfully']);
//     });
// });

$router->group(['prefix'=>'template', 'middleware'=>'auth'], function () use ($router) {
    $router->put('create', ['uses' => 'PosTemplateController@create']);
    $router->post('update', ['uses' => 'PosTemplateController@update']);
    // $router->delete('delete/{id}', function($id) {
    //     PosTemplate::findOrFail($id)->delete();
    //     return response()->json(['message' => 'Template Deleted Success']);
    // });
});

$router->group(['prefix' => 'vouchers', 'middleware'=>'auth'], function() use ($router) {
    $router->get('', ['uses' => 'VoucherController@select']);
    $router->put('create', ['uses' => 'VoucherController@create']);
    $router->post('update', ['uses' => 'VoucherController@update']);
    $router->delete('delete/{id}', function($id) {
        $voucher = Voucher::findOrFail($id);
        $voucher->state = false;
        return response()->json(['message' => 'Voucher Deleted Successfully']);
    });
});


$router->group(['prefix'=>'customers', 'middleware'=>'auth'], function() use ($router) {
    // $router->get('', function() {
    //     return response()->json(Customer::all());
    // });
    $router->put('create', ['uses' => 'CustomerController@create']);
    $router->post('update', ['uses' => 'CustomerController@update']);
    // $router->delete('delete/{id}', function($id) {
    //     Customer::findOrFail($id)->delete();
    //     return response()->json(['message' => 'Customer Deleted Successfully']);
    // });
});

$router->group(['prefix'=>'invoices'], function() use ($router) {
    $router->get('', ['uses' => 'InvoiceController@select']);
    $router->put('create', ['uses' => 'InvoiceController@create']);
    $router->post('transactions', ['uses' => 'InvoiceController@createTransactions']);
    $router->delete('delete/{id}', ['uses' => 'InvoiceController@delete']);
});


$router->group(['prefix' => 'get'], function() use ($router) {
    $router->get('bundles', ['uses' => 'BundleController@select']);
    $router->post('general-item', ['uses' => 'GeneralItemController@select']);
    $router->get('ledgers', function(){ return response()->json(Ledger::all()); });
    $router->get('products', ['uses' => 'ProductController@select']);
    $router->get('stocks', function() { return response()->json(Stock::all()); });
});

$router->group(['prefix' => 'create', 'middleware' => 'auth'], function () use ($router) {
    $router->post('bundle', ['uses' => 'BundleController@create']);
    $router->post('bundle/template', ['uses' => 'BundleController@createTemplate']);
    $router->post('product/stock-usage', ['uses' => 'ProductController@addStockTempate']);
    $router->post('stock', ['uses' => 'StockController@create']);
});

$router->group(['prefix' => 'update', 'middleware' => 'auth'], function () use ($router) {
    $router->put('bundle', ['uses' => 'BundleController@update']);
});

$router->group(['prefix' => 'delete', 'middleware' => 'auth'], function () use ($router) {
    $router->delete('bundle/{$id}', ['uses' => 'BundleController@delete']);
    $router->delete('bundle/template/{$id}', ['uses'=> 'BundleController@deleteTemplate']);
});

<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\PosItem;
use App\Models\PosTemplate;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Support\Facades\Cache;

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
    $router->get('balance/{id}', ['uses' => 'LedgerController@selectBalance']);
    $router->put('balance/create', ['uses' => 'LedgerController@updateBalance']);
    $router->get('users', function() {return User::all('id', 'displayName');});
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
    $router->get('', function() {
        $products = Cache::remember('products', 600, function(){
            return Product::all();
        });
        return response()->json($products);
    });
    $router->put('create', ['uses' => 'ProductController@create']);
    $router->post('update', ['uses' => 'ProductController@update']);
    $router->delete('delete/{id}', ['uses' => 'ProductConroller@delete']);
});

$router->group(['prefix'=>'pos-items', 'middleware'=>'auth'], function() use ($router) {
    $router->get('', ['uses' => 'PosItemController@select']);
    $router->put('create', ['uses' => 'PosItemController@create']);
    $router->post('update', ['uses' => 'PosItemController@update']);
    $router->delete('delete/{id}', function($id) {
        $posItem = PosItem::findOrFail($id);
        PosTemplate::where('positem_id', $posItem->id)->delete();
        $posItem->delete();

        return response()->json(['message'=>'PosItem Deleted Successfully']);
    });
});

$router->group(['prefix'=>'template', 'middleware'=>'auth'], function () use ($router) {
    $router->put('create', ['uses' => 'PosTemplateController@create']);
    $router->post('update', ['uses' => 'PosTemplateController@update']);
    $router->delete('delete/{id}', function($id) {
        PosTemplate::findOrFail($id)->delete();
        return response()->json(['message' => 'Template Deleted Success']);
    });
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
    $router->get('', function() {
        return response()->json(Customer::all());
    });
    $router->put('create', ['uses' => 'CustomerController@create']);
    $router->post('update', ['uses' => 'CustomerController@update']);
    $router->delete('delete/{id}', function($id) {
        Customer::findOrFail($id)->delete();
        return response()->json(['message' => 'Customer Deleted Successfully']);
    });
});

$router->group(['prefix'=>'invoices', 'middleware'=>'auth'], function() use ($router) {
    $router->get('', ['uses' => 'InvoiceController@select']);
    $router->put('create', ['uses' => 'InvoiceController@create']);
    $router->post('transactions', ['uses' => 'InvoiceController@createTransactions']);
});
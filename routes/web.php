<?php

use App\Models\PosItem;
use App\Models\PosTemplate;
use App\Models\Product;
use App\Models\User;
use App\Models\Voucher;

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

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->get('usernames/{username}', function($username) {
        $count = User::where('username', $username)->get()->count();
        return response($count);
    });
});

$router->group(['prefix'=>'users'], function() use ($router) {
    $router->post('login', ['uses' => 'UserController@login']);
    $router->put('create', ['uses' => 'UserController@register']);
});

$router->group(['prefix'=>'ledgers'], function() use ($router) {
    $router->get('', ['uses' => 'LedgerController@select']);
    $router->put('create', ['uses' => 'LedgerController@create']);
    $router->post('update', ['uses' => 'LedgerController@update']);
});

$router->group(['prefix'=>'products'], function() use ($router) {
    $router->get('', function() {
        return response()->json(Product::all());
    });
    $router->put('create', ['uses' => 'ProductController@create']);
    $router->post('update', ['uses' => 'ProductController@update']);
    $router->delete('delete/{id}', ['uses' => 'ProductConroller@delete']);
});

$router->group(['prefix'=>'pos-items'], function() use ($router) {
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

$router->group(['prefix'=>'template'], function () use ($router) {
    $router->put('create', ['uses' => 'PosTemplateController@create']);
    $router->post('update', ['uses' => 'PosTemplateController@update']);
    $router->delete('delete/{id}', function($id) {
        PosTemplate::findOrFail($id)->delete();
        return response()->json(['message' => 'Template Deleted Success']);
    });
});

$router->group(['prefix' => 'vouchers'], function() use ($router) {
    $router->get('', ['uses' => 'VoucherController@select']);
    $router->put('create', ['uses' => 'VoucherController@create']);
    $router->post('update', ['uses' => 'VoucherController@update']);
    $router->delete('delete/{id}', function($id) {
        $voucher = Voucher::findOrFail($id);
        $voucher->state = false;
        return response()->json(['message' => 'Voucher Deleted Successfully']);
    });
});
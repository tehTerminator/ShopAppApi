<?php

use App\Models\Balance;
use App\Models\Ledger;
use App\Models\User;
use Carbon\Carbon;

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

$router->group(['prefix'=>'user'], function() use ($router) {
    $router->get('/', function() {
        return response()->json(User::all());
    });
    $router->post('login', ['uses' => 'UserController@login']);
    $router->put('register', ['uses' => 'UserController@register']);
});

$router->group(['prefix'=>'ledger'], function() use ($router) {
    $router->get('/', function() {
        $ledgers = Ledger::with(['balance' => function($query) {
            $query->whereDate('created_at', Carbon::now());
        }])->get();
        return response()->json($ledgers);
    });
});
<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix(config('storefront.api.routing.prefix', 'storefront'))->namespace('Fleetbase\Storefront\Http\Controllers')->group(
    function ($router) {
        /*
        |--------------------------------------------------------------------------
        | Internal Storefront API Routes
        |--------------------------------------------------------------------------
        |
        | Primary internal routes for console.
        */
        $router->prefix(config('storefront.api.routing.internal_prefix', 'int'))->group(
            function ($router) {
                $router->group(
                    ['prefix' => 'v1', 'middleware' => ['fleetbase.protected']],
                    function ($router) {
                        $router->get('/', 'ActionController@welcome');
                        $router->group(
                            ['prefix' => 'actions'],
                            function ($router) {
                                $router->get('store-count', 'ActionController@getStoreCount');
                                $router->get('metrics', 'ActionController@getMetrics');
                            }
                        );
                        $router->fleetbaseRoutes('stores');
                        $router->fleetbaseRoutes('customers');
                        $router->fleetbaseRoutes(
                            'drivers',
                            function ($router, $controller) {
                                $router->get('statuses', $controller('statuses'));
                            }
                        );
                    }
                );
            }
        );
    }
);

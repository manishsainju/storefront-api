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
                $router->group(['prefix' => 'int/v1', 'middleware' => ['internal.cors']], function () use ($router) {
                    $router->get('networks/find/{id}', 'NetworkController@findNetwork');
                });

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
                        $router->fleetbaseRoutes(
                            'orders',
                            function ($router, $controller) {
                                $router->post('accept', $controller('acceptOrder'));
                                $router->post('ready', $controller('markOrderAsReady'));
                                $router->post('completed', $controller('markOrderAsCompleted'));
                            }
                        );
                        $router->fleetbaseRoutes(
                            'networks',
                            function ($router, $controller) {
                                $router->delete('{id}/remove-category', $controller('deleteCategory'));
                                $router->post('{id}/set-store-category', $controller('ddStoreToCategory'));
                                $router->post('{id}/add-stores', $controller('addStores'));
                                $router->post('{id}/remove-stores', $controller('removeStores'));
                                $router->post('{id}/invite', $controller('sendInvites'));
                            }
                        );
                        $router->fleetbaseRoutes('customers');
                        $router->fleetbaseRoutes('stores');
                        $router->fleetbaseRoutes('store-hours');
                        $router->fleetbaseRoutes('store-locations');
                        $router->fleetbaseRoutes(
                            'products',
                            function ($router, $controller) {
                                $router->post('process-imports', $controller('processImports'));
                            }
                        );
                        $router->fleetbaseRoutes('product-hours');
                        $router->fleetbaseRoutes('product-variants');
                        $router->fleetbaseRoutes('product-variant-options');
                        $router->fleetbaseRoutes('product-addons');
                        $router->fleetbaseRoutes('product-addon-categories');
                        $router->fleetbaseRoutes('addon-categories');
                        $router->fleetbaseRoutes('gateways');
                        $router->fleetbaseRoutes('notification-channels');
                        $router->fleetbaseRoutes('reviews');
                        $router->fleetbaseRoutes('votes');
                    }
                );
            }
        );
    }
);

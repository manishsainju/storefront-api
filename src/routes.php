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
        | Consumable FleetOps API Routes
        |--------------------------------------------------------------------------
        |
        | End-user API routes, these are routes that the SDK and applications will interface with, and require API credentials.
        */
        Route::prefix('v1')
            ->middleware('storefront.api')
            ->namespace('v1')
            ->group(function ($router) {
                $router->get('about', 'v1\StoreController@about');
                $router->get('locations/{id}', 'v1\StoreController@location');
                $router->get('locations', 'v1\StoreController@locations');
                $router->get('gateways/{id}', 'v1\StoreController@gateway');
                $router->get('gateways', 'v1\StoreController@gateways');
                $router->get('search', 'v1\StoreController@search');
                $router->get('stores', 'v1\NetworkController@stores');
                $router->get('store-locations', 'v1\NetworkController@storeLocations');
                $router->get('tags', 'v1\NetworkController@tags');


                // storefront/v1/checkouts
                $router->group(['prefix' => 'checkouts'], function () use ($router) {
                    $router->get('before', 'v1\CheckoutController@beforeCheckout');
                    $router->get('capture', 'v1\CheckoutController@captureOrder');
                });

                // storefront/v1/service-quotes
                $router->group(['prefix' => 'service-quotes'], function () use ($router) {
                    $router->get('from-cart', 'v1\ServiceQuoteController@fromCart');
                });

                // storefront/v1/categories
                $router->group(['prefix' => 'categories'], function () use ($router) {
                    $router->get('/', 'v1\CategoryController@query');
                });

                // storefront/v1/products
                $router->group(['prefix' => 'products'], function () use ($router) {
                    $router->get('/', 'v1\ProductController@query');
                    $router->get('{id}', 'v1\ProductController@find');
                });

                // storefront/v1/reviews
                $router->group(['prefix' => 'reviews'], function () use ($router) {
                    $router->get('/', 'v1\ReviewController@query');
                    $router->get('count', 'v1\ReviewController@count');
                    $router->get('{id}', 'v1\ReviewController@find');
                    $router->post('/', 'v1\ReviewController@create');
                    $router->delete('{id}', 'v1\ReviewController@find');
                });

                // storefront/v1/customers
                $router->group(['prefix' => 'customers'], function () use ($router) {
                    $router->put('{id}', 'v1\CustomerController@update');
                    $router->get('/', 'v1\CustomerController@query');
                    $router->post('register-device', 'v1\CustomerController@registerDevice');
                    $router->get('places', 'v1\CustomerController@places');
                    $router->get('orders', 'v1\CustomerController@orders');
                    $router->get('{id}', 'v1\CustomerController@find');
                    $router->post('/', 'v1\CustomerController@create');
                    $router->post('login-with-sms', 'v1\CustomerController@loginWithPhone');
                    $router->post('verify-code', 'v1\CustomerController@verifyCode');
                    $router->post('login', 'v1\CustomerController@login');
                    $router->post('request-creation-code', 'v1\CustomerController@requestCustomerCreationCode');
                });

                // hotfix! storefront-app sending customer update to /contacts/ route
                $router->put('contacts/{id}', 'v1\CustomerController@update');

                // storefront/v1/carts
                $router->group(['prefix' => 'carts'], function () use ($router) {
                    $router->get('/', 'v1\CartController@retrieve');
                    $router->get('{uniqueId}', 'v1\CartController@retrieve');
                    $router->put('{cartId}/empty', 'v1\CartController@empty');
                    $router->post('{cartId}/{productId}', 'v1\CartController@add');
                    $router->put('{cartId}/{lineItemId}', 'v1\CartController@update');
                    $router->delete('{cartId}/{lineItemId}', 'v1\CartController@remove');
                    $router->delete('{cartId}', 'v1\CartController@delete');
                });
            });
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
                        $router->group(
                            [],
                            function ($router) {
                                /** Dashboard Build */
                                $router->get('dashboard', 'MetricsController@dashboard');

                                $router->group(
                                    ['prefix' => 'metrics'],
                                    function ($router) {
                                        $router->get('all', 'MetricsController@all');
                                    }
                                );
                            }
                        );
                    }
                );
            }
        );
    }
);

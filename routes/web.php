<?php

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

$router->get('/export', 'OrderRefundController@export');

$router->group(['prefix' => 'api/'], function ($app) {
	$app->post('login','AuthController@login');

	$app->group(['prefix' => 'users', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','AuthController@list');
		$app->post('detail','AuthController@detail');
		$app->post('create','AuthController@register');
		$app->post('update','AuthController@update');
		$app->post('delete','AuthController@delete');
		$app->get('trash','AuthController@trash');
		$app->post('change-password','AuthController@changePassword');
	});

	$app->group(['prefix' => 'customers', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','CustomerController@list');
		$app->post('detail','CustomerController@detail');
		$app->post('create','CustomerController@create');
		$app->put('update','CustomerController@update');
		$app->post('delete','CustomerController@delete');
		$app->get('trash','CustomerController@trash');
	});

	$app->group(['prefix' => 'vendors', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','VendorController@list');
		$app->post('detail','VendorController@detail');
		$app->post('create','VendorController@create');
		$app->put('update','VendorController@update');
		$app->post('delete','VendorController@delete');
		$app->get('trash','VendorController@trash');
	});

	$app->group(['prefix' => 'drivers', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','DriverController@list');
		$app->post('detail','DriverController@detail');
		$app->post('create','DriverController@create');
		$app->post('update','DriverController@update');
		$app->post('delete','DriverController@delete');
		$app->post('change-password','DriverController@changePassword');
		$app->get('trash','DriverController@trash');
	});

	$app->group(['prefix' => 'menus', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','MenuController@list');
		$app->post('detail','MenuController@detail');
		$app->post('create','MenuController@create');
		$app->post('update','MenuController@update');
		$app->post('delete','MenuController@delete');
		$app->get('trash','MenuController@trash');
	});

	$app->group(['prefix' => 'roles', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','RoleController@list');
		$app->post('detail','RoleController@detail');
		$app->post('create','RoleController@create');
		$app->post('update','RoleController@update');
		$app->post('delete','RoleController@delete');
		$app->get('trash','RoleController@trash');
	});

	$app->group(['prefix' => 'broadcasts', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','BroadcastController@list');
		$app->get('dashboard','BroadcastController@dashboard');
		$app->post('detail','BroadcastController@detail');
		$app->post('create','BroadcastController@create');
		$app->post('update','BroadcastController@update');
		$app->post('delete','BroadcastController@delete');
		$app->get('trash','BroadcastController@trash');
	});

	$app->group(['prefix' => 'trucks', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','TruckController@list');
		$app->get('feets','TruckController@getTruckFeets');
		$app->get('types','TruckController@getTruckType');
		$app->get('boxes','TruckController@getBoxType');
		$app->post('detail','TruckController@detail');
		$app->post('create','TruckController@create');
		$app->put('update','TruckController@update');
		$app->post('delete','TruckController@delete');
		$app->get('trash','TruckController@trash');

		$app->group(['prefix' => 'prices', 'middleware' => 'jwt.auth'], function ($app) {
			$app->get('/','TruckPriceController@list');
			$app->post('search','TruckPriceController@detail');
			$app->post('create','TruckPriceController@createPriceAndPromo');
			$app->post('create-by-excel','TruckPriceController@createPriceAndPromoByExcel');
			$app->put('update','TruckPriceController@updatePriceAndPromo');
			$app->post('delete','TruckPriceController@deletePriceAndPromo');
			$app->get('trash','TruckPriceController@trash');		
		});

		$app->group(['prefix' => 'stocks', 'middleware' => 'jwt.auth'], function ($app) {
			$app->get('/','TruckStockController@list');
			$app->post('search','TruckStockController@detail');
			$app->post('create','TruckStockController@create');
			$app->put('update','TruckStockController@update');
			$app->post('delete','TruckStockController@delete');
			$app->get('trash','TruckStockController@trash');		
		});
	});

	$app->group(['prefix' => 'promotions', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','PromotionController@list');
		$app->post('search','PromotionController@detail');
		$app->post('create','PromotionController@create');
		$app->put('update','PromotionController@update');
		$app->post('delete','PromotionController@delete');
		$app->get('trash','PromotionController@trash');
	});

	$app->group(['prefix' => 'locations', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('provinces','LocationController@province');
		$app->post('regencies','LocationController@regency');
		$app->post('districts','LocationController@district');
		$app->get('districts/all','LocationController@getAllDistrict');
	});

	$app->group(['prefix' => 'time-limits', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','TimeLimitController@list');
		$app->post('detail','TimeLimitController@detail');
		$app->post('create','TimeLimitController@create');
		$app->put('update','TimeLimitController@update');
		$app->post('delete','TimeLimitController@delete');
		$app->get('trash','TimeLimitController@trash');
	});

	$app->group(['prefix' => 'time-operation', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','TimeOperationController@list');
		$app->post('detail','TimeOperationController@detail');
		$app->post('create','TimeOperationController@create');
		$app->put('update','TimeOperationController@update');
		$app->post('delete','TimOperationtController@delete');
		$app->get('trash','TimeOperationController@trash');
	});

	$app->group(['prefix' => 'orders', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','OrderController@list');
		$app->get('verified','OrderController@verifiedOrder');
		$app->get('confirmations','OrderController@getOrderConfirmation');
		$app->post('payment-confirmation/{action}','OrderController@confirmationPayment');
		$app->post('document-confirmation/{action}','OrderController@confirmationDocument');
		$app->get('list-monitoring','OrderController@listMonitoring');
		$app->post('assign','OrderController@assign');
		$app->post('detail','OrderController@getOrderInDetail');
		$app->get('available-trucks/{vendor_id}/{truck_type_id}/{truck_feet_id}','OrderController@availableTruck');
		$app->get('available-driver/{vendor_id}','OrderController@assignDriver');

		$app->group(['prefix' => 'refunds', 'middleware' => 'jwt.auth'], function ($app) {
			$app->get('{status}','OrderRefundController@listRefund');
			$app->post('/update','OrderRefundController@updateAmount');
			$app->post('/','OrderRefundController@refund');
		});		
	});

	$app->group(['prefix' => 'depots', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','DepotController@list');
		$app->post('detail','DepotController@detail');
		$app->post('create','DepotController@create');
		$app->put('update','DepotController@update');
		$app->post('delete','DepotController@delete');
		$app->get('trash','DepotController@trash');
	});

	$app->group(['prefix' => 'seaports', 'middleware' => 'jwt.auth'], function ($app) {
		$app->get('/','SeaportController@list');
		$app->post('detail','SeaportController@detail');
		$app->post('create','SeaportController@create');
		$app->put('update','SeaportController@update');
		$app->post('delete','SeaportController@delete');
		$app->get('trash','SeaportController@trash');
	});

	$app->group(['prefix' => 'customer-services', 'middleware' => 'jwt.auth'], function ($app) {
		$app->group(['prefix' => 'settings', 'middleware' => 'jwt.auth'], function ($app) {
			$app->get('/','CustomerServiceController@list');
			$app->post('detail','CustomerServiceController@detail');
			$app->post('create','CustomerServiceController@create');
			$app->put('update','CustomerServiceController@update');
			$app->post('delete','CustomerServiceController@delete');
			$app->get('trash','CustomerServiceController@trash');
		});
	});
});
<?php

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
$router->post('api/v1/child-birth','ChildBirthController@store');
$router->get('api/v1/child-birth/{id}','ChildBirthController@detail');
$router->put('api/v1/child-birth/{id}','ChildBirthController@update');
$router->delete('api/v1/child-birth/{id}','ChildBirthController@destroy');

$router->get('api/v1/birth-history','ChildBirthController@birthHistory');
$router->get('api/v1/report','ChildBirthController@report');
$router->get('api/v1/report/{year}/annual','ChildBirthController@annualReport');
$router->get('api/v1/report/{year}/monthly/{month}','ChildBirthController@monthlyReport');

<?php

use App\Http\Controllers\PingController;
use App\Http\Controllers\TravelRequestController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Routing\Router;

/* @var $router Router */

$router->name('v1.')->group(function (Router $router) {
    $router->get('/ping', [PingController::class, 'show'])->name('ping.show');
    $router->post('/login', [LoginController::class, 'login'])->name('login');
    $router->post('/register', [RegisterController::class, 'register'])->name('register');

    $router->middleware('auth:jwt')->group(function (Router $router) {
        $router->group(['prefix' => 'travel-requests'], function (Router $router) {
            $router->get('/', [TravelRequestController::class, 'index'])->name('travel-requests.index');
            $router->post('/', [TravelRequestController::class, 'store'])->name('travel-requests.store');

            $router->group(['prefix' => '/{travelRequest}'], function (Router $router) {
                $router->get('/', [TravelRequestController::class, 'show'])->name('travel-requests.show');
                $router->put('/', [TravelRequestController::class, 'update'])->name('travel-requests.update');
            });
        });
    });
});

<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');
$routes->get('/', 'Game::index');
$routes->post('walk', 'Steps::walk');
$routes->get('battle', 'Battle::index');
$routes->post('battle/start', 'Battle::start');
$routes->post('battle/attack', 'Battle::attack');
$routes->post('battle/flee', 'Battle::flee');
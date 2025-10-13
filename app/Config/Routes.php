<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('ahp', 'AHP::index');
$routes->post('ahp/calculate', 'AHP::calculate');
$routes->get('topsis', 'TOPSIS::index');
$routes->post('topsis/calculate', 'TOPSIS::calculate');
$routes->get('saw', 'SAW::index');
$routes->post('saw/calculate', 'SAW::calculate');
$routes->get('wp', 'WP::index');
$routes->post('wp/calculate', 'WP::calculate');
$routes->get('compare', 'Compare::index');
$routes->post('compare/calculate', 'Compare::calculate');
$routes->get('/', 'Home::index');
$routes->get('dss', 'DSSController::index');
$routes->get('dss/input/(:alpha)', 'DSSController::input/$1');
$routes->post('dss/calculate', 'DSSController::calculate');

// WP Routes
$routes->get('wp', 'WPController::index');
$routes->post('wp/calculate', 'WPController::calculate');

$routes->get('dss/export', 'DSSController::export');
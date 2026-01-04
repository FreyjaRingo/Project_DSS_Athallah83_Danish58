<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('ahp', 'Ahp::index');
$routes->post('ahp/calculate', 'Ahp::calculate');
$routes->get('topsis', 'Topsis::index');
$routes->post('topsis/calculate', 'Topsis::calculate');
$routes->get('saw', 'Saw::index');
$routes->post('saw/calculate', 'Saw::calculate');
$routes->get('wp', 'Wp::index');
$routes->post('wp/calculate', 'Wp::calculate');
$routes->get('compare', 'Compare::index');
$routes->post('compare/calculate', 'Compare::calculate');
$routes->get('dss', 'DSSController::index');
$routes->get('dss/input/(:alpha)', 'DSSController::input/$1');
$routes->post('dss/calculate', 'DSSController::calculate');

// WP Routes
$routes->get('wp', 'WPController::index');
$routes->post('wp/calculate', 'WPController::calculate');
$routes->get('dss/export', 'DSSController::export');
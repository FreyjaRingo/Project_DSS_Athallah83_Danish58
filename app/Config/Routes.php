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
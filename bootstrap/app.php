<?php

require_once '../vendor/autoload.php';

// Start the slim app
$app = new \Slim\App();

// DI Container
$container = $app->getContainer();

// Save the root or absolute path of this application
$container['abs_path'] = realpath(__DIR__ . '/../');

// The urls Extension asks for this
$container['site_url'] = function() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    return $protocol . $_SERVER['HTTP_HOST'];
};

$container['view'] = function($c) {
    $view = new \Slim\Views\Twig($c['abs_path'] . '/resources/views', [
        'cache' => false
    ]);

    $view->addExtension(new App\Twig\TwigExtension($c));

    return $view;
};

// Test home route
$app->get('/', function ($request, $response) {
    return $this->view->render($response, 'home.twig');
})->setName('home');

// Run App
$app->run();
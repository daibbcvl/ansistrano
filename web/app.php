<?php

use Symfony\Component\HttpFoundation\Request;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup
// for more information
//umask(0000);

require __DIR__.'/../vendor/autoload.php';
if (PHP_VERSION_ID < 70000) {
    include_once __DIR__.'/../var/bootstrap.php.cache';
}

$kernel = new AppKernel('prod', false);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();

// Real client IP is under HTTP_X_FORWARDED_FOR for requests through AWS ELB,
// i.e. REMOTE_ADDR holds AWS ELB IP instead
Request::setTrustedProxies(
    // trust *all* requests
    ['127.0.0.1', $request->server->get('REMOTE_ADDR')],
    // if you're using ELB, otherwise see https://symfony.com/doc/current/request/load_balancer_reverse_proxy.html
    Request::HEADER_X_FORWARDED_AWS_ELB
);

$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);

<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;

$app = new Application();
$app->register(new RoutingServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());

$app->register(new \Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\LocaleServiceProvider());

$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        return $app['request_stack']->getMasterRequest()->getBasepath() . '/' . $asset;
    }));

    return $twig;
});

$app['couchdb.client'] = function ($app) {
    return \Doctrine\CouchDB\CouchDBClient::create($app['couchdb.options']);
};

$app['blog.posts'] = function ($app) {
    return new \Blog\Repos\Posts($app['couchdb.client']);
};

$app['blog.comments'] = function ($app) {
    return new \Blog\Repos\Comments($app['couchdb.client']);
};

return $app;

<?php

// configure your app for the production environment

$app['twig.path'] = array(__DIR__.'/../templates');
//$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

$app['couchdb.options'] = array(
    'dbname' => 'silex_blog',
    'type' => 'socket',
    'host' => 'localhost',
    'port' => 5984,
    'user' => null,
    'password' => null,
    'ip' => null,
    'logging' => true
);

<?php

$index = $app['controllers_factory'];
$index->get('/', function () use ($app) {
    return $app['twig']->render('index.html', array());
})->bind('homepage');

return $index;

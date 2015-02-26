<?php

use \Symfony\Component\HttpFoundation\Request;

$history = $app['controllers_factory'];


$history->get('/', function () use ($app) {
    return $app['twig']->render(
        'blog/history/index.twig',
        array('years' => $app['blog.posts']->countAllByYear())
    );
})->bind('history');

$history->get('/year/{year}', function ($year) use ($app) {
    return $app['twig']->render(
        'blog/history/year.twig',
        array('year' => (int)$year, 'months' => $app['blog.posts']->countAllByMonth($year))
    );
})->bind('history_year');

$history->get('/year/{year}/month/{month}', function ($year, $month) use ($app) {
    return $app['twig']->render(
        'blog/history/month.twig',
        array('year' => (int)$year, 'month' => (int)$month, 'days' => $app['blog.posts']->countAllByDay($year, $month))
    );
})->bind('history_month');

$history->get('/date/{year}/{month}/{day}', function ($year, $month = null, $day = null) use ($app) {
    return $app['twig']->render(
        'blog/posts/index.twig',
        array('posts' => $app['blog.posts']->getAllByDate($year, $month, $day))
    );
})->bind('history_date')->value('month', null)->value('day', null);

return $history;

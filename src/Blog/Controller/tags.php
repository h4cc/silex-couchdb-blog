<?php

use \Symfony\Component\HttpFoundation\Request;

$tags = $app['controllers_factory'];


$tags->get('/{tag}', function ($tag) use ($app) {
    return $app['twig']->render(
        'blog/posts/index.twig',
        array('posts' => $app['blog.posts']->getAllByTag($tag))
    );
})->bind('tag');

$tags->get('/', function () use ($app) {
    return $app['twig']->render(
        'blog/tags/list.twig',
        array('tags' => $app['blog.posts']->getTagsWithCount())
    );
})->bind('tags');


return $tags;

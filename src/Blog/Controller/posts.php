<?php

use \Symfony\Component\HttpFoundation\Request;

$posts = $app['controllers_factory'];

$posts->match('/add', function (Request $request) use ($app) {
    // some default data for when the form is displayed the first time
    $data = array(
        'title' => 'A Title',
        'content' => 'Some Content. Lorem Ipsum ....',
        'tags' => 'tag1, tag2, tag3'
    );

    $form = $app['form.factory']->createBuilder('form', $data)
        ->add('title')
        ->add('content')
        ->add('tags')
        ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();

        $post = $app['blog.posts']->create();
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $tags = array();
        foreach (explode(',', $data['tags']) as $tag) {
            $tag = trim($tag);
            if ($tag) {
                $tags[] = $tag;
            }
        }
        $post->setTags($tags);

        $app['blog.posts']->addPost($post);

        // redirect somewhere
        return $app->redirect($app['url_generator']->generate('homepage'));
    }

    // display the form
    return $app['twig']->render('blog/posts/add.twig', array('form' => $form->createView()));
})->bind('posts_add')->method('GET|POST');


$posts->post('/{id}/comment', function ($id, Request $request) use ($app) {

    $data = array(
        'author' => 'Your Name',
        'comment' => '',
    );

    $form = $app['form.factory']->createBuilder('form', $data)
        ->add('author')
        ->add('comment')
        ->getForm();

    $post = $app['blog.posts']->getById($id);
    if (!$post) {
        $app->abort(404, 'Post not found');
    }

    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();

        $comment = $app['blog.comments']->create();
        $comment->setAuthor($data['author']);
        $comment->setComment($data['comment']);

        $app['blog.comments']->addCommentToPost($comment, $post);
    }

    return $app->redirect($app['url_generator']->generate('post_read', array('id' => $post->getId())));
})->bind('post_comment_add');

$posts->match('/{id}', function ($id) use ($app) {

    $data = array(
        'author' => 'Your Name',
        'comment' => '',
    );

    $form = $app['form.factory']->createBuilder('form', $data)
        ->add('author')
        ->add('comment')
        ->getForm();

    $post = $app['blog.posts']->getById($id);
    if (!$post) {
        $app->abort(404, 'Post not found');
    }

    $comments = $app['blog.comments']->getCommentsForPost($post);

    return $app['twig']->render(
        'blog/posts/read.twig',
        array('post' => $post, 'comments' => $comments, 'comment_form' => $form->createView())
    );
})->bind('post_read')->method('GET');


$posts->match('/', function () use ($app) {
    return $app['twig']->render(
        'blog/posts/index.twig',
        array('posts' => $app['blog.posts']->getAll())
    );
})->bind('posts')->method('GET');


return $posts;

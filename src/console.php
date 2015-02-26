<?php

//var_dump(date_create('monday last week 1 hour')); die();

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$console = new Application('Blog Silex using CouchDB', 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);

$console
    ->register('db:create')
    ->setDefinition(array())
    ->setDescription('Creating database')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $client = $app['couchdb.client'];
        try {
            $client->deleteDatabase($app['couchdb.options']['dbname']);
        }catch(\Exception $e) {}
        $client->createDatabase($app['couchdb.options']['dbname']);

        $designDocs = array(
            new \Blog\Designs\Posts(),
            new \Blog\Designs\Comments(),
        );

        foreach($designDocs as $designDoc) {
            $client->createDesignDocument($designDoc->getName(), $designDoc);
        }
    })
;

$console
    ->register('db:fixtures')
    ->setDefinition(array())
    ->setDescription('Creating database')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $posts = array(
            array('First post', 'Hello world!', array('first', 'hello'), array(
                array('Julius', 'Hooray, first post!'),
            )),
            array('Second post', 'Some content', array('second', 'content'), array(
                array('Julius', 'Great content!'),
            )),
            array('Third post', 'More content', array('third', 'content'), array(
                array('Julius', 'I love your blog!'),
            )),
        );
        $hour = 0;
        foreach($posts as $postData) {
            $post = $app['blog.posts']->create();
            $post->setTitle($postData[0]);
            $post->setContent($postData[1]);
            $post->setTags($postData[2]);
            $post->setCreated('monday last week '.$hour.' hour');
            $app['blog.posts']->addPost($post);

            foreach($postData[3] as $commentData) {
                $comment = $app['blog.comments']->create();
                $comment->setAuthor($commentData[0]);
                $comment->setComment($commentData[1]);
                $comment->setCreated('monday last week '.++$hour.' hour');
                $app['blog.comments']->addCommentToPost($comment, $post);
            }
            $hour += 13;
        }
    })
;

return $console;

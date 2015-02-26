<?php


namespace Blog\Repos;

use Blog\Entity\Comment;
use Blog\Entity\Post;
use Doctrine\CouchDB\CouchDBClient;

class Comments
{

    private $couchdb;

    public function __construct(CouchDBClient $couchdb)
    {
        $this->couchdb = $couchdb;
    }

    public function create()
    {
        return new Comment();
    }

    public function addCommentToPost(Comment $comment, Post $post)
    {
        $comment->setPostId($post->getId());

        list($id, $rev) = $this->couchdb->postDocument($comment->toArray());

        $comment->setId($id);
        $comment->setRev($rev);

        return $comment;
    }

    public function getCommentsForPost(Post $post)
    {
        $query = $this->couchdb->createViewQuery('comments', 'by_post');

        $query->setDescending(false);
        $query->setReduce(false);
        $query->setStartKey(array($post->getId()));
        $query->setEndKey(array($post->getId().CouchDBClient::COLLATION_END));

        return array_map(function ($data) {
            return $this->hibernateComment($data['value']);
        }, $query->execute()->toArray());
    }

    private function hibernateComment($data, Comment $comment = null) {
        if(!$comment) {
            $comment = $this->create();
        }
        $comment->fromArray($data);
        return $comment;
    }
}
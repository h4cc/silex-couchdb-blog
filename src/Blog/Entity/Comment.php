<?php

namespace Blog\Entity;

class Comment extends AbstractDocument
{
    private $author;
    private $comment;
    private $created;
    private $postId;

    public function __construct()
    {
        $this->created = new \DateTime('now');
    }

    public function getType()
    {
        return 'blog.comment';
    }

    public function toArray()
    {
        return (parent::toArray() + array(
                'postId' => $this->postId,
                'author' => $this->author,
                'comment' => $this->comment,
                'created' => date_format($this->created, 'c')
            ));
    }

    public function fromArray(array $data)
    {
        $this->setAuthor($data['author']);
        $this->setPostId($data['postId']);
        $this->setComment($data['comment']);
        $this->setCreated($data['created']);

        parent::fromArray($data);
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = new \DateTime($created);
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @param mixed $postId
     */
    public function setPostId($postId)
    {
        $this->postId = $postId;
    }
}
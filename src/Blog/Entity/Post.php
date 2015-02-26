<?php

namespace Blog\Entity;

class Post extends AbstractDocument
{

    private $title;
    private $content;
    private $created;
    private $tags = array();

    public function __construct()
    {
        $this->created = new \DateTime('now');
    }

    public function getType()
    {
        return 'blog.post';
    }

    public function toArray()
    {
        return (parent::toArray() + array(
                'title' => $this->title,
                'content' => $this->content,
                'created' => date_format($this->created, 'c'),
                'tags' => $this->tags,
            ));
    }

    public function fromArray(array $data)
    {
        $this->setTitle($data['title']);
        $this->setContent($data['content']);
        $this->setCreated($data['created']);
        $this->setTags($data['tags']);

        parent::fromArray($data);
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}
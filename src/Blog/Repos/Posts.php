<?php



namespace Blog\Repos;


use Blog\Entity\Post;
use Doctrine\CouchDB\CouchDBClient;

class Posts
{

    private $couchdb;

    public function __construct(CouchDBClient $couchdb)
    {
        $this->couchdb = $couchdb;
    }

    public function create()
    {
        return new Post();
    }

    public function addPost(Post $post)
    {
        list($id, $rev) = $this->couchdb->postDocument($post->toArray());

        $post->setId($id);
        $post->setRev($rev);

        return $post;
    }

    public function getAll()
    {
        $query = $this->couchdb->createViewQuery('posts', 'by_date');

        $query->setDescending(true);
        $query->setReduce(false);
        $query->setIncludeDocs(true);

        return array_map(function ($data) {
            return $this->hibernatePost($data['doc']);
        }, $query->execute()->toArray());
    }

    public function countAllByYear()
    {
        $query = $this->couchdb->createViewQuery('posts', 'by_date');
        $query->setReduce(true);
        $query->setGroupLevel(1);
        return array_map(function ($data) {
            return array(
                'year' => $data['key'][0],
                'count' => $data['value']
            );
        }, $query->execute()->toArray());
    }

    public function countAllByMonth($year)
    {
        $query = $this->couchdb->createViewQuery('posts', 'by_date');

        $query->setReduce(true);
        $query->setGroupLevel(2);

        $query->setStartKey(array((int)$year));
        $query->setEndKey(array((int)$year+1));
        $query->setInclusiveEnd(false);

        return array_map(function ($data) {
            return array(
                'month' => $data['key'][1],
                'count' => $data['value']
            );
        }, $query->execute()->toArray());
    }

    public function countAllByDay($year, $month)
    {
        $query = $this->couchdb->createViewQuery('posts', 'by_date');

        $query->setReduce(true);
        $query->setGroupLevel(3);

        $query->setStartKey(array((int)$year, (int)$month));
        $query->setEndKey(array((int)$year, (int)$month+1));
        $query->setInclusiveEnd(false);

        return array_map(function ($data) {
            return array(
                'day' => $data['key'][2],
                'count' => $data['value']
            );
        }, $query->execute()->toArray());
    }

    public function getAllByDate($year, $month = null, $day = null)
    {
        $query = $this->couchdb->createViewQuery('posts', 'by_date');

        $startkey = array();
        $startkey[] = (int)$year;
        if ($month) {
            $endkey[] = (int)$year;
            $startkey[] = (int)$month;
            if ($day) {
                $endkey[] = (int)$month;
                $startkey[] = (int)$day;
                $endkey[] = (int)$day+1;
            }else{
                $endkey[] = (int)$month+1;
            }
        }else{
            $endkey[] = (int)$year+1;
        }

        $query->setStartKey($startkey);
        $query->setEndKey($endkey);

        $query->setIncludeDocs(true);
        $query->setReduce(false);
        $query->setInclusiveEnd(false);

        //var_dump($query->execute()->toArray());

        return array_map(function ($data) {
            return $this->hibernatePost($data['doc']);
        }, $query->execute()->toArray());
    }

    public function getAllByTag($tag)
    {
        $query = $this->couchdb->createViewQuery('posts', 'by_tag');

        $query->setIncludeDocs(true);
        $query->setDescending(true);
        $query->setReduce(false);
        $query->setStartKey(array($tag . CouchDBClient::COLLATION_END));
        $query->setEndKey(array($tag));

        return array_map(function ($data) {
            return $this->hibernatePost($data['doc']);
        }, $query->execute()->toArray());
    }

    public function getTagsWithCount()
    {
        $query = $this->couchdb->createViewQuery('posts', 'by_tag');

        $query->setReduce(true);
        $query->setGroupLevel(1);

        return array_map(function ($data) {
            return array(
                'name' => $data['key'][0],
                'count' => $data['value'],
            );
        }, $query->execute()->toArray());
    }

    public function getById($id)
    {
        $result = $this->couchdb->findDocument($id);

        if (isset($result->body['error'])) {
            return null;
        }

        return $this->hibernatePost($result->body);
    }

    private function hibernatePost($data, Post $post = null)
    {
        if (!$post) {
            $post = $this->create();
        }
        $post->fromArray($data);
        return $post;
    }
}
<?php

namespace Blog\Designs;

use Doctrine\CouchDB\View\DesignDocument;

class Posts implements DesignDocument
{
    public function getName()
    {
        return 'posts';
    }
    public function getData()
    {
        return array(
            'language' => 'javascript',
            'views' => array(
                'by_date' => array(
                    'map' => 'function(doc) {
                        if(\'blog.post\' == doc.type) {
                            var date = new Date(Date.parse(doc.created));
                            emit([date.getFullYear(), date.getMonth() + 1, date.getDate()], doc._id);
                        }
                    }',
                    'reduce' => '_count'
                ),
                'by_tag' => array(
                    'map' => 'function(doc) {
                        if(\'blog.post\' == doc.type) {
                            doc.tags.forEach(function(tag) {
                                emit([tag, doc.created], doc._id);
                            });
                        }
                    }',
                    'reduce' => '_count'
                ),
            ),
        );
    }
}
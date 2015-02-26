<?php

namespace Blog\Designs;

use Doctrine\CouchDB\View\DesignDocument;

class Comments implements DesignDocument
{
    public function getName()
    {
        return 'comments';
    }
    public function getData()
    {
        return array(
            'language' => 'javascript',
            'views' => array(
                'by_post' => array(
                    'map' => 'function(doc) {
                        if(\'blog.comment\' == doc.type) {
                            emit([doc.postId, doc.created], doc);
                        }
                    }',
                    'reduce' => '_count'
                ),
            ),
        );
    }
}
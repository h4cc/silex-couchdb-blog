<?php

namespace Blog;

use Doctrine\CouchDB\View\DesignDocument;

class DatabaseDesign implements DesignDocument
{

    /**
     * Get design doc code
     *
     * Return the view (or general design doc) code, which should be
     * committed to the database, which should be structured like:
     *
     * <code>
     *  array(
     *    "views" => array(
     *      "name" => array(
     *          "map"     => "code",
     *          ["reduce" => "code"],
     *      ),
     *      ...
     *    )
     *  )
     * </code>
     */
    public function getData()
    {
        return array(

        );
    }
}
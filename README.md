# Simple Blog using Silex and CouchDB

## About

I wrote this blog to learn some more about CouchDB in a real world example.
Some of my learnings are collected in this README.

## Installation

Install CouchDB if not yet done: https://launchpad.net/~couchdb/+archive/ubuntu/stable

```
composer install
php bin/console db:create
php bin/console db:fixtures
php -S 127.0.0.1:8000 -t web/
```

The now hopefully working application will be available at: http://127.0.0.1:8000/

## CouchDB

My main learning part was on couchdb.


### Controllers

The controllers in `src/Blog/Controller` look as any other controllers for such a frontend would look like. Nothing new to expect here.

### Entities

The Entities in `src/Blog/Entities` look quite common too, except the fact i needed a method `getType()` which will return a string like `blog.post`. This field is needed to distinquish the documents in cochdb from each other.

Also two Methods `toArray()` and `fromArray()` were needed, so the mapping from Object to JSON and back for CouchDB could be done.

#### Date Values

JSON and following that CouchDB too, has no format for dates. Because of this anything that JavaScripts `Date.parse()` will work with is ok.

So i used `date_format($this->created, 'c')` to create a date string with so useful order of `YYYY-MM-DD` inside.

Another good idea, is to store dates as timestamps in UTC. This will also enable queries by range, but not counting by range.

### Repositories

The repos in `src/Blog/Repos` simple use the CouchDBClient to talk to CouchDB.

#### POST and PUT

POST will create new documents, while PUT will create new Revisions of existing Documents.

### Queries

All the queries i used where asking views with `createViewQuery`.

Using the `_all_docs` resource was not needed at any time.

### Designs

The Design Documents in `src/Blog/Design` are a simple representation of what queries to the server will look like.

#### _design/posts/_view/by_tag

```
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
```

This view does the following:

1. It will only contain documents of type == blog.post.
2. Multiple entries per document will be created, for each tag one.
3. The key of the entries in the views are a array because:
  * The first key is the tag, so a query by tag can be done like this:
        ```
        $query->setStartKey(array($tag));
        $query->setEndKey(array($tag.CouchDBClient::COLLATION_END));
        ```
  * The second key is the current date, this allows sorting by date because it is in the form `YYYY-MM-DD`.
      If a reverse query is needed, simply use `$query->setDescending(false);` and switch the arguments of `setStartKey` and `setEndKey`.
4. The emitted value is just the document id, because this way not the complete document has to be stored again.
   The document can still be received using `include_docs=true`.
  * You can either emit the while document in a view like this `emit(doc._id, doc);` which will DUPLICATE the data on the disk.
  * Our you can only emit the key of the document, which will need to do FETCH these documents from the main database for each query.
5. Using the reduce function `_count`, because it is nearly no overhead, but has some nice features:
  * It enables queries that return the number of entries for one tag, which can be used for a overview of used tags: http://127.0.0.1:8000/tags/
  * The reduce function can easily by circumvented by `$query->setReduce(false);`, which will query the non-reduced dataset.


#### _design/posts/_view/by_date

```
'by_date' => array(
    'map' => 'function(doc) {
        if(\'blog.post\' == doc.type) {
            var date = new Date(Date.parse(doc.created));
            emit([date.getFullYear(), date.getMonth() + 1, date.getDate()], doc._id);
        }
    }',
    'reduce' => '_count'
),
```

Next to `by_tag`, this view does another trick:

1. Using the key `[date.getFullYear(), date.getMonth() + 1, date.getDate()]` will result in a key like `[2015, 2, 26]`.
2. This allows queries like:
    * All documents in 2015
    * Count all documents in 2015
    * All documents in February 2015
    * Count all documents in February 2015
    * All documents from Feb to April 2015
    * All documents at 26. Feb 2015
    * All documents from 26. to 28. Feb 2015


#### _design/posts/_view/by_date

```
'by_post' => array(
    'map' => 'function(doc) {
        if(\'blog.comment\' == doc.type) {
            emit([doc.postId, doc.created], doc);
        }
    }',
    'reduce' => '_count'
),
```

This view does nothing new, but something to realize:

1. There are no JOINS in CouchDB.
2. This view will enable a query that can count or fetch all comments by postId ordered by timestamp.
    * Good thing about HTTP: They can be done asynchronously and parallel.

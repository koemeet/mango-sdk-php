> This is something I needed for my own projects and decided to open source it since it may be useful for others! It is still in very early stages, so use it on your own risk.

# A PHP client for JSON-API compliant API's
I don't like working with transformed json arrays. I want them to be simple POPO's (Plain Old PHP Objects). Inspired by Doctrine I used proxies to achieve lazy loading and using a simple UOW (Unit Of Work) that is aware of the objects created. Utilizing the idea of an identity map there are no unnecessary http requests.

It can also manage refresh tokens for you! So when the current access/refresh token set is not valid anymore, it can automatically requests new ones without you having to do this manually.

### Simple example
```php
$registry = new ResourceRegistry();
$registry->add('posts', Post::class);
$registry->add('comments', Comment::class);

$client = new Client($registry, 'your_client_id', 'your_client_secret');

// You will get a php array containing `Post` objects
$posts = $client->query('posts', [
    'page' => 2,
    'limit' => 4
];

echo $posts->getTitle();

// if the comments where not included in the response, it will lazy load them for you!
// there has to be a relationship link for has-many, for simply belongs-to it isn't required.
foreach ($posts->getComments() as $comment) {
    echo $comment->getMessage();
}

// find single comment by id
$comment = $client->find('comments', 4);

// lazy load the related post. If this was the same post we requested earlier it will fetch it from the identity map to prevent a http request.
echo $comment->getPost()->getTitle();
```

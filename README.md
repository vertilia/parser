# parser

Different text-related parsing mechanisms, like creating regexps for path parsing, extracting templates variables etc.

Needed to generate a regexp for a particular path, like when generating a router table from an [OpenApi](https://github.com/OAI/OpenAPI-Specification) specification.

Please see [URI Templates](https://www.rfc-editor.org/rfc/rfc6570) for in-depth explanation of patterns syntax.

```php
<?php

$parser = new OpenApiParser();

// simple pattern

$pattern = $parser->getRegex('/users/{id}');
echo $pattern;
// outputs: #^/users/(?P<id>[^:/?\#\[\]@!$&'()*+;=]+)$#

$result = preg_match($pattern, '/users/123,234', $matches);
echo $matches['id'];
// outputs: 123,234

// complex pattern

$pattern = $parser->getRegex(
    '/users{id}/friends{friend}',
    [
        'id' => ['style' => 'matrix'],
        'friend' => ['style' => 'label', 'explode' => true],
    ]
);

preg_match(
    $pattern,
    '/users;id=3,4,5/friends.6.7.8',
    $matches
);
echo $matches['id'], ' - ', $matches['friend'];
// outputs: ;id=3,4,5 - .6.7.8
```

See tests for usage examples.

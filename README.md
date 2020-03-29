# parser

Different text-related parsing mechanisms, like creating regexps for path parsing, extracting templates variables etc.

Needed to generate a regexp for a particulal path, like when generating a router table from an OpenApi specification.

```php
<?php

$parser = new OpenApiParser();
$pattern = $parser->getRegex('/users/{id}');
echo $pattern; // outputs: #^/users/(?P<id>.+)$#

$result = preg_match($pattern, '/users/123,234', $matches);
echo $matches['id']; // outputs: 123,234
```

See tests for more examples.

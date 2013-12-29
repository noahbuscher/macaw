Macaw
=====

Macaw is a simple PHP router. It's super small, fast, and sexy.

### Examples

Macaw is not an object, so you can just make direct operations to the class. Here's the Hello World:

```PHP
Macaw::get('/', function() {
  echo 'Hello world!';
});

Macaw::dispatch();
```

Macaw also supports lambda URIs, such as:

```PHP
Macaw::get('/(:any)', function($slug) {
  echo 'The slug is: ' . $slug;
});

Macaw::dispatch();
```

You can also make requests for HTTP methods in Macaw, so you could also do:

```PHP
Macaw::get('/', function() {
  echo 'I <3 GET commands!';
});

Macaw::post('/', function() {
  echo 'I <3 POST commands!';
});

Macaw::dispatch();
```

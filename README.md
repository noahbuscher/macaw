Macaw
=====

Macaw is a simple, open source PHP router. It's super small (~150 LOC), fast, and sexy. This class allows you to jus throw it into your project and start using it immediately.

### Install

If you have Composer, just include Macaw as a project dependency in your `composer.json`. If you don't just install it by downloading the .ZIP file and extracting it to your project directory.

```
require: {
    "noahbuscher/macaw": "dev-master"
}
```

### Examples

First, `use` the Macaw namespace:

```PHP
use \NoahBuscher\Macaw\Macaw;
```

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

Lastly, if there is no route defined for a certain location, you can make Macaw run a custom callback, like:

```PHP
Macaw::error(function() {
  echo '404 :: Not Found';
});
```

If you don't specify an error callback, Macaw will just echo `404`.

<hr>

In order to let the server know the URI does not point to a real file, you need to use the included [.htaccess](http://httpd.apache.org/docs/2.2/howto/htaccess.html) file.

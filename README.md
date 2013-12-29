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

Lastly, if there is no route defined for a certian location, you can make Macaw run a custom callback, like:

```PHP
Macaw::error(function() {
  echo '404 :: Not Found';
});
```

If you don't specify an error callback, Macaw will just echo `404`.

<hr>

In order to let the server know the URI does not point to a real file, you need to create a [.htaccess](http://httpd.apache.org/docs/2.2/howto/htaccess.html) file. Put it in the parent folder of your project.

```
Options -indexes

<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d

    RewriteRule ^(.*)$ index.php?/$1 [L]
</IfModule>

<IfModule !mod_rewrite.c>
    ErrorDocument 404 index.php
</IfModule>
```

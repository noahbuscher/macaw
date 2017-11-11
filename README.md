Macaw
=====

Macaw is a simple, open source PHP router. It's super small (~150 LOC), fast, and has some great annotated source code. This class allows you to just throw it into your project and start using it immediately.

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
  echo 'I'm a GET request!';
});

Macaw::post('/', function() {
  echo 'I'm a POST request!';
});

Macaw::any('/', function() {
  echo 'I can be both a GET and a POST request!';
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

In order to let the server know the URI does not point to a real file, you may need to use one of the example [configuration files](https://github.com/noahbuscher/Macaw/blob/master/config).


## Example passing to a controller instead of a closure
<hr>
It's possible to pass the namespace path to a controller instead of the closure:

For this demo lets say I have a folder called controllers with a demo.php

index.php:

```php
require('vendor/autoload.php');

use NoahBuscher\Macaw\Macaw;

Macaw::get('/', 'Controllers\demo@index');
Macaw::get('page', 'Controllers\demo@page');
Macaw::get('view/(:num)', 'Controllers\demo@view');

Macaw::dispatch();
```

demo.php:

```php
<?php
namespace controllers;

class Demo {

    public function index()
    {
        echo 'home';
    }

    public function page()
    {
        echo 'page';
    }

    public function view($id)
    {
        echo $id;
    }

}
```

This is with Macaw installed via composer.

composer.json:

```
{
   "require": {
        "noahbuscher/macaw": "dev-master"
    },
    "autoload": {
        "psr-4": {
            "" : ""
        }
    }
}
````

.htaccess(Apache):

```
RewriteEngine On
RewriteBase /

# Allow any files or directories that exist to be displayed directly
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

RewriteRule ^(.*)$ index.php?$1 [QSA,L]
```

.htaccess(Nginx):

```
rewrite ^/(.*)/$ /$1 redirect;

if (!-e $request_filename){
	rewrite ^(.*)$ /index.php break;
}

```

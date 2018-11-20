# Zipkin - Tracing Analysis 链路追踪

zipkin链路分析

## Example

### FastD

* 配置
```shell
cp vendor/fastd/zipkin/src/Config/zipkin.php config/zipkin.php

vim config/app.php
```

```php

    'services' => [
        \FastD\ServiceProvider\CacheServiceProvider::class,
        \FastD\ServiceProvider\LoggerServiceProvider::class,
        \FastD\ServiceProvider\RouteServiceProvider::class,
        
        // add Zipkin
        \FastD\Zipkin\Provider\ZipkinProvider::class,
    ],

```

* usage

```php
    
    // use callback
    $response = app()->get('zipkin')->childSpan(
        function () use () {
            // do something
            return something
    
            return $response;
        }, 'server name', 'SERVER or CLIENT or ...', annotate[string or array], tag[array]
    );

    //or use method
    app()->get('zipkin')->child($name, $kind = Zipkin::SERVER, $annotate = null, array $tag = [])
    // do something
     app()->get('zipkin')->childFinished()
     
```

### other

```php
require __DIR__ . '/vendor/autoload.php';

use FastD\Zipkin\Span;

$span = new Span();

$span->instance(string $name, $options = [], $isParent = true);

// do something

$span->childSpan(callable $request, $name, $kind = Zipkin::SERVER, $annotate = null, array $tag = [])

// do something

register_shutdown_function(
    function () use ($span) {
        $span->finised();
    }
);

```

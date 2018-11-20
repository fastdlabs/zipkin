<?php
/**
 * @author: ZhaQiu <34485431@qq.com>
 * @time: 2018/11/16
 */

namespace FastD\Zipkin\Provider;

use FastD\Container\Container;
use FastD\Container\ServiceProviderInterface;
use FastD\Zipkin\Middleware\ZipkinMiddleware;
use FastD\Zipkin\Span;

/**
 * Class ZipkinProvider
 * @package Provider
 */
class ZipkinProvider implements ServiceProviderInterface
{

    /**
     * @param Container $container
     */
    public function register(Container $container)
    {

        $zipkin = array_merge(
            load(app()->getPath().'/config/zipkin.php'),
            config()->get('zipkin', [])
        );

        config()->merge([
            'zipkin' => $zipkin,
        ]);

        $container->add('zipkin', new Span());

        $container->get('dispatcher')->before(new ZipkinMiddleware());
    }
}

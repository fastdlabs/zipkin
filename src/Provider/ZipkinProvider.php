<?php
/**
 * @author: ZhaQiu <34485431@qq.com>
 * @time: 2018/11/16
 */

namespace Provider;

use FastD\Container\Container;
use FastD\Container\ServiceProviderInterface;
use FastD\Zipkin\Middleware\ZipkinMiddleware;

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
        $container->get('dispatcher')->before(new ZipkinMiddleware());
    }
}

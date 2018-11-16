<?php
/**
 * @author: ZhaQiu <34485431@qq.com>
 * @time: 2018/11/15
 */

namespace FastD\Zipkin\Middleware;

use FastD\Middleware\DelegateInterface;
use FastD\Middleware\Middleware;
use FastD\Zipkin\Zipkin;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ZipkinMiddleware extends Middleware
{

    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface $next
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request, DelegateInterface $next)
    {
        Zipkin::createZipkin(
            config()->get('zipkin.name', app()->getName()),
            config()->get('zipkin.options')
        );

        $response = $next->process($request);

        Zipkin::finish();
        Zipkin::flush();

        return $response;
    }
}
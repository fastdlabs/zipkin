<?php
/**
 * @author: ZhaQiu <34485431@qq.com>
 * @time: 2018/11/15
 */

namespace FastD\Zipkin\Middleware;

use FastD\Middleware\DelegateInterface;
use FastD\Middleware\Middleware;
use FastD\Zipkin\Queue\SpanQueue;
use FastD\Zipkin\Server\HttpTaskServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ZipkinMiddleware
 * @package FastD\Zipkin\Middleware
 */
class ZipkinMiddleware extends Middleware
{

    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface $next
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request, DelegateInterface $next)
    {
        app()->get('zipkin')->instance(
            app()->getName(),
            config()->get('zipkin.options', []),
            config()->get('zipkin.is_parent', true)
        );

        try {
            $response = $next->process($request);
        } finally {
            // 考虑上报时的性能损失，这里使用task, 前提需要使用 HttpTaskServer::class 作为HttpServer，目前仅支持Http
            if (app()->has('server') && HttpTaskServer::class === config()->get('server')) {
                server()->getSwoole()->task(new SpanQueue());
            } else {
                app()->get('zipkin')->finished();
            }
        }

        return $response;
    }
}

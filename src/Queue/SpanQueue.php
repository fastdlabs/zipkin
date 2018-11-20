<?php
/**
 * @author: ZhaQiu <34485431@qq.com>
 * @time: 2018/11/20
 */

namespace FastD\Zipkin\Queue;

/**
 * Class SpanQueue
 */
class SpanQueue
{
    public function run()
    {
        app()->get('zipkin')->finished();
    }
}

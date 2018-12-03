<?php
/**
 * @author: ZhaQiu <34485431@qq.com>
 * @time: 2018/11/20
 */

namespace FastD\Zipkin\Server;

use FastD\Servitization\Server\HTTPServer;
use swoole_server;
use Wangjian\Queue\Job\AbstractJob;

class HttpTaskServer extends HTTPServer
{
    public function doTask(swoole_server $server, $data, $taskId, $workerId)
    {
        try {
            $data->run();
        } catch (\Exception $exception) {
            if ($data instanceof AbstractJob) {
                queue()->push($data);
            }
            logger()->error('zipkin: ' . $exception->getMessage());
        }
    }
}

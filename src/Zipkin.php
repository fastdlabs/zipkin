<?php
/**
 * @author: ZhaQiu <34485431@qq.com>
 * @time: 2018/11/14
 */

namespace FastD\Zipkin;

use const Zipkin\Kind\CLIENT;
use const Zipkin\Kind\SERVER;
use function Zipkin\Timestamp\now;
use Zipkin\DefaultTracing;
use Zipkin\Endpoint;
use Zipkin\Propagation\B3;
use Zipkin\Propagation\DefaultSamplingFlags;
use Zipkin\Propagation\Map;
use Zipkin\Reporters\Http;
use Zipkin\Reporters\Http\CurlFactory;
use Zipkin\Samplers\BinarySampler;
use Zipkin\Span;
use Zipkin\Tracer;
use Zipkin\Tracing;
use Zipkin\TracingBuilder;

/**
 * Class Zipkin
 * @package FastD\Zipkin
 */
class Zipkin
{

    /**
     * @var DefaultTracing
     */
    protected $zipkin;
    /**
     * @var Tracer
     */
    protected static $tracer;
    /**
     * @var Tracing
     */
    protected static $tracing;
    /**
     * @var Callable
     */
    protected static $injector;
    /**
     * @var Span
     */
    protected static $span;
    /**
     * @var Span
     */
    protected static $childSpan;

    /**
     * @var
     */
    protected static $appName;

    /**
     * create the tracing
     *
     * @param $name
     * @param array $options
     */
    public static function createZipkin($name, $options = [])
    {
        self::$appName = $name;
        $endpoint = Endpoint::create(app()->getName(), get_local_ip(), null, config()->get('server.host'));
        $reporter = new Http(CurlFactory::create(), $options);
        $sampler = BinarySampler::createAsAlwaysSample();

        self::$tracing = TracingBuilder::create()
            ->havingLocalEndpoint($endpoint)
            ->havingSampler($sampler)
            ->havingReporter($reporter)
            ->build();
    }

    /**
     * 调度顶层实例
     */
    public static function newTrace()
    {
        $defaultSamplingFlags = DefaultSamplingFlags::createAsSampled();
        self::$tracer = self::$tracing->getTracer();
        $span = self::$tracer->newTrace($defaultSamplingFlags);
        $span->start();
        $span->setName(self::$appName);
        $span->setKind(SERVER);
        self::$span = $span;
    }

    /**
     * 继承父级调度
     */
    public static function nextSpan()
    {
        $carrier = self::getCarrier(request()->getHeaders());

        $extractor = self::$tracing->getPropagation()->getExtractor(new Map());
        $extractedContext = $extractor($carrier);

        self::$tracer = self::$tracing->getTracer();
        $span = self::$tracer->nextSpan($extractedContext);
        $span->start();
        $span->setName(self::$appName);
        $span->setKind(SERVER);
        self::$span = $span;
    }


    /**
     * 获取父级载体
     *
     * @param array $carrier
     * @return array
     */
    public static function getCarrier(array $carrier)
    {
        return [
            'x-b3-traceid' => $carrier['x_b3_traceid'][0],
            'x-b3-spanid' => $carrier['x_b3_spanid'][0],
            'x-b3-parentspanid' => $carrier['x_b3_parentspanid'][0],
            'x-b3-sampled' => current($carrier['x_b3_sampled']),
            'x-b3-flags' => current($carrier['x_b3_flags']),
        ];
    }

    /**
     * 构建父级载体
     *
     * @return array
     */
    public static function setCarrier()
    {
        return [
            B3::TRACE_ID_NAME => self::$childSpan->getContext()->getTraceId(),
            B3::SPAN_ID_NAME => self::$childSpan->getContext()->getSpanId(),
            B3::PARENT_SPAN_ID_NAME => self::$childSpan->getContext()->getParentId(),
            B3::SAMPLED_NAME => 1,
            B3::FLAGS_NAME => 1,
        ];
    }

    /**
     * 子span - 用于调用链顶层
     */
    public static function newChild()
    {
        self::$childSpan = self::$tracer->newChild(self::$span->getContext());
        self::$childSpan->start();
    }

    /**
     * 子span调用类型[client, server]
     * @param string $kind
     */
    public static function setChildKind(string $kind = CLIENT)
    {
        self::$childSpan->setKind($kind);
    }

    /**
     * 子span命名
     *
     * @param string $name
     */
    public static function setChildName(string $name = 'get_switch')
    {
        self::$childSpan->setName($name);
    }

    /**
     * 子span注释
     *
     * @param string $annotate
     */
    public static function childAnnotate(string $annotate)
    {
        self::$childSpan->annotate($annotate, now());
    }

    /**
     * 子span调用完成
     */
    public static function childFinish()
    {
        self::$childSpan->finish();
    }

    public static function injector()
    {
        $carrier = [];
        $injector = self::$tracing->getPropagation()->getInjector(new Map());
        // @todo 子span 父span关联
        $injector(self::$childSpan->getContext(), $carrier);

    }

    /**
     * 调用链关联参数
     *
     * @return array
     */
    public static function carrier()
    {
        return [
            B3::TRACE_ID_NAME => self::$childSpan->getContext()->getTraceId(),
            B3::SPAN_ID_NAME => self::$childSpan->getContext()->getSpanId(),
            B3::PARENT_SPAN_ID_NAME => self::$childSpan->getContext()->getParentId(),
            B3::SAMPLED_NAME => 1,
            B3::FLAGS_NAME => 1,
        ];
    }


    /**
     * 父级span调用完成
     */
    public static function finish()
    {
        self::$span->finish();
    }

    /**
     * @param string $annotate
     */
    public static function annotate(string $annotate = 'request_started')
    {
        self::$span->annotate($annotate, now());
    }

    /**
     * 提交至服务
     */
    public static function flush()
    {
        self::$tracer->flush();
    }
}

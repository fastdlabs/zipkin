<?php
/**
 * @author: ZhaQiu <34485431@qq.com>
 * @time: 2018/11/19
 */
namespace FastD\Zipkin\Contracts;

/**
 * Interface SpanInterface
 * @package FastD\Zipkin\Contracts
 */
interface SpanInterface
{
    public static function createZipkin($name, $options = [], $isParent = true);

    public static function newTrace();

    public static function nextSpan();

    public static function getCarrier(array $carrier = []);

    public static function setCarrier();

    public static function finish();

    public static function flush();
}

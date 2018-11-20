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
interface ChildSpanInterface
{

    public static function newChild();

    public static function setChildKind();

    public static function setChildName();

    public static function childAnnotate(string $annotate);

    public static function childFinish();
}

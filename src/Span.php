<?php
/**
 * @author: ZhaQiu <34485431@qq.com>
 * @time: 2018/11/19
 */

namespace FastD\Zipkin;

/**
 * Class Span
 * @package FastD\Zipkin
 */
class Span
{

    /**
     * @param string $name
     * @param array $options
     * @param bool $isParent
     */
    public function instance(string $name, $options = [], $isParent = true)
    {
        Zipkin::createZipkin(
            $name,
            $options,
            $isParent
        );
    }

    /**
     * @param callable $request
     * @param $name
     * @param null $annotate
     * @param array $tag
     * @return mixed
     */
    public function childSpan(callable $request, $name, $kind = Zipkin::SERVER, $annotate = null, array $tag = [])
    {
        if (is_array($annotate)) {
            $start = current($annotate);
            $end = next($annotate);
        } else {
            $start = $annotate;
            $end = false;
        }

        $this->child($name, $kind, $start, $tag);

        try {
            return $request();
        } finally {
            $end && Zipkin::childAnnotate($end);
            $this->childFinished();
        }
    }

    public function child($name, $kind = Zipkin::SERVER, $annotate = null, array $tag = [])
    {
        Zipkin::newChild();
        Zipkin::setChildKind($kind);
        Zipkin::setChildName($name);
        Zipkin::injector();
        !is_null($annotate) && Zipkin::childAnnotate($annotate);
        !empty($tag) && Zipkin::childTag($tag);
    }

    public function childFinished()
    {
        Zipkin::childFinish();
    }

    public function finished()
    {
        Zipkin::finish();
        Zipkin::flush();
    }
}

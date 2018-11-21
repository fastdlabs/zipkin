<?php
/**
 * @author: ZhaQiu <34485431@qq.com>
 * @time: 2018/11/20
 */
return [
    'span' => [
        'name' => 'app_default',
        'is_parent' => false,
        'options' => [
            'endpoint_url' => 'http://localhost:9411/api/v2/spans',
            'kind' => \FastD\Zipkin\Zipkin::CONSUMER
        ]
    ]
];

<?php

namespace Vertilia\Parser;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Vertilia\Parser\OpenApiParser
 */
class OpenApiParserTest extends TestCase
{
    /**
     * @param string $pattern
     * @param array $options
     * @param string $path
     * @param array $vars
     * @covers ::getRegex
     * @dataProvider getRegexProvider
     */
    public function testGetRegex(string $pattern, array $options, string $path, array $vars)
    {
        $parser = new OpenApiParser();
        $pattern = $parser->getRegex($pattern, $options);
        preg_match($pattern, $path, $matches);
        $this->assertEquals($vars, array_intersect_key($matches, $vars));
    }

    /** data provider */
    public function getRegexProvider(): array
    {
        $path_scalar_array_object = '/scalar/{scalar}/array/{array}/object/{object}';
        $query_scalar_array_object = '{scalar}{array}{object}';
        $query_scalar_array_id_name = '{scalar}{array}{id,name}';

        return [
            ['/users/', [], '/users/', []],

            // style = simple
            ['/users/{id}', [], '/users/5', ['id' => '5']],
            [
                '/users/{id}/friends/{friend}',
                [],
                '/users/3,4,5/friends/6,7,8',
                ['id' => '3,4,5', 'friend' => '6,7,8'],
            ],

            // default {style: "simple", explode: false}
            [
                $path_scalar_array_object,
                ['scalar' => [], 'array' => [], 'object' => []],
                '/scalar/1/array/4,5,6/object/id,10,name,John',
                ['scalar' => '1', 'array' => '4,5,6', 'object' => 'id,10,name,John'],
            ],

            // {style: "simple", explode: true}
            [
                $path_scalar_array_object,
                ['scalar' => ['explode' => true], 'array' => ['explode' => true], 'object' => ['explode' => true]],
                '/scalar/1/array/4,5,6/object/id=10,name=John',
                ['scalar' => '1', 'array' => '4,5,6', 'object' => 'id=10,name=John'],
            ],

            // {style: "label", explode: false}
            [
                $path_scalar_array_object,
                ['scalar' => ['style' => 'label'], 'array' => ['style' => 'label'], 'object' => ['style' => 'label']],
                '/scalar/.1/array/.4,5,6/object/.id,10,name,John',
                ['scalar' => '.1', 'array' => '.4,5,6', 'object' => '.id,10,name,John'],
            ],

            // {style: "label", explode: true}
            [
                $path_scalar_array_object,
                ['scalar' => ['style' => 'label', 'explode' => true], 'array' => ['style' => 'label', 'explode' => true], 'object' => ['style' => 'label', 'explode' => true]],
                '/scalar/.1/array/.4.5.6/object/.id=10.name=John',
                ['scalar' => '.1', 'array' => '.4.5.6', 'object' => '.id=10.name=John'],
            ],

            // {style: "matrix", explode: false}
            [
                $path_scalar_array_object,
                ['scalar' => ['style' => 'matrix'], 'array' => ['style' => 'matrix'], 'object' => ['style' => 'matrix']],
                '/scalar/;scalar=1/array/;array=4,5,6/object/;object=id,10,name,John',
                ['scalar' => ';scalar=1', 'array' => ';array=4,5,6', 'object' => ';object=id,10,name,John'],
            ],

            // {style: "matrix", explode: true}
            [
                $path_scalar_array_object,
                ['scalar' => ['style' => 'matrix', 'explode' => true], 'array' => ['style' => 'matrix', 'explode' => true], 'object' => ['style' => 'matrix', 'explode' => true]],
                '/scalar/;scalar=1/array/;array=4;array=5;array=6/object/;id=10;name=John',
                ['scalar' => ';scalar=1', 'array' => ';array=4;array=5;array=6', 'object' => ';id=10;name=John'],
            ],

            // default {style: "form", explode: true}
            [
                $query_scalar_array_id_name,
                ['scalar' => ['in' => 'query', 'style' => 'form'], 'array' => ['in' => 'query', 'style' => 'form'], 'id,name' => ['in' => 'query', 'style' => 'form']],
                '?scalar=1&array=4&array=5&array=6&id=10&name=John',
                ['scalar' => '?scalar=1', 'array' => '&array=4&array=5&array=6', 'id_name' => '&id=10&name=John'],
            ],

            // {style: "form", explode: false}
            [
                $query_scalar_array_object,
                ['scalar' => ['in' => 'query', 'style' => 'form', 'explode' => false], 'array' => ['in' => 'query', 'style' => 'form', 'explode' => false], 'object' => ['in' => 'query', 'style' => 'form', 'explode' => false]],
                '?scalar=1&array=4,5,6&object=id,10,name,John',
                ['scalar' => '?scalar=1', 'array' => '&array=4,5,6', 'object' => '&object=id,10,name,John'],
            ],
        ];
    }
}

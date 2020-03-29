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
     * @param string $path
     * @param array $vars
     * @covers ::getRegex
     * @dataProvider getRegexProvider
     */
    public function testGetRegex($pattern, $path, $vars)
    {
        $parser = new OpenApiParser();
        $pattern = $parser->getRegex($pattern);
        $result = preg_match($pattern, $path, $matches);
        $this->assertEquals($vars, array_intersect_key($matches, $vars));
    }

    /** data provider */
    public function getRegexProvider()
    {
        return [
            ['/users/', '/users/', []],
            ['/users/{id}', '/users/5', ['id' => '5']],
            [
                '/users/{id}/friends/{friend}',
                '/users/3,4,5/friends/6,7,8',
                ['id' => '3,4,5', 'friend' => '6,7,8'],
            ],
            [
                '/users/{id*}/friends{.friend*}',
                '/users/3,4,5/friends.6.7',
                ['id' => '3,4,5', 'friend' => '.6.7'],
            ],
            [
                '/users{;id}/friends{.friend,enemy*}',
                '/users;id=3,4,5/friends.friend=6.friend=7.enemy=8',
                ['id' => ';id=3,4,5', 'friend_enemy' => '.friend=6.friend=7.enemy=8'],
            ],
        ];
    }
}

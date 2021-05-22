<?php
declare(strict_types=1);

namespace Vertilia\Parser;

/**
 * Parses source parameters
 *
 * @see https://swagger.io/docs/specification/serialization/
 * @see https://tools.ietf.org/html/rfc6570
 */
class OpenApiParser implements ParserInterface
{
    protected array $vars = [];

    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * Returns a regexp that allows to match the incoming route and parse provided params.
     * Ex: "/users/{id}" will be represented by regexp "#^/users/(?P<id>.+)$#"
     *
     * @param string $text
     * @return string
     */
    public function getRegex(string $text): string
    {
        $this->vars = [];
        $cnt = 0;

        return '#^'.preg_replace_callback(
            '/\\\{(\\\?\W)?([[:alpha:]_]\w*(?:,[[:alpha:]_]\w*)*)(\\\\\*)?\\\}/',
            function ($v) use (&$cnt) {
                $this->vars[++$cnt] = $v[2];
                $var = strtr($v[2], ',', '_');
                switch ($v[1]) {
                    case '\.':
                        return "(?P<$var>\\..+)";
                    case ';':
                        return "(?P<$var>;.+)";
                    default:
                        return "(?P<$var>.+)";
                }
            },
            preg_quote($text, '#')
        ).'$#';
    }
}

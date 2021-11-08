<?php
declare(strict_types=1);

namespace Vertilia\Parser;

/**
 * Parses source parameters
 *
 * @see https://swagger.io/docs/specification/serialization/
 */
class OpenApiParser extends UriTemplateParser
{
    /**
     * Returns a regexp that allows to match the incoming route and parse provided params.
     * Ex: "/users/{id}" will be represented by regexp "#^/users/(?P<id>[^/]+)$#"
     * Receives
     *
     * @param string $text string with vars, ex: "/users/{id}"
     * @param array|null $var_options options per var, ex: {"id": {"style": "label"}}
     * @return string
     * @see UriTemplateParser
     */
    public function getRegex(string $text, array $var_options = null): string
    {
        if ($var_options) {
            foreach ($var_options as $var => $options) {
                // define prefix
                switch ($options['in'] ?? 'path') {
                    case 'path':
                        switch ($options['style'] ?? null) {
                            case 'label':
                                $prefix = '.';
                                break;
                            case 'matrix':
                                $prefix = ';';
                                break;
                            default:
                                $prefix = '';
                        }
                        break;
                    case 'query':
                        $prefix = !empty($options['allowReserved']) ? '+' : '?';
                        if (!isset($options['explode'])) {
                            $options['explode'] = true; // default "explode" = true
                        }
                        break;
                    default:
                        $prefix = '';
                }

                // define suffix
                $suffix = !empty($options['explode']) ? '*' : '';

                // update var template
                if ($prefix or $suffix) {
                    $text = str_replace("{{$var}}", "{{$prefix}$var$suffix}", $text);
                }
            }
        }

        return parent::getRegex($text);
    }
}

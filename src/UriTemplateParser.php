<?php
declare(strict_types=1);

namespace Vertilia\Parser;

/**
 * Parses source parameters
 *
 * @see https://tools.ietf.org/html/rfc6570
 */
class UriTemplateParser implements ParserInterface
{
    const CLASS_UNRESERVED = '[^:/?\\#\\[\\]@!$&\'()*+,;=]';
    const CLASS_UNRESERVED_COMMA = '[^:/?\\#\\[\\]@!$&\'()*+;=]';
    const CLASS_UNRESERVED_EQUAL = '[^:/?\\#\\[\\]@!$&\'()*+,;]';
    const CLASS_UNRESERVED_COMMA_EQUAL = '[^:/?\\#\\[\\]@!$&\'()*+;]';
    const CLASS_UNRESERVED_SEMICOLON_EQUAL = '[^:/?\\#\\[\\]@!$&\'()*+,]';
    const CLASS_UNRESERVED_AMP_EQUAL = '[^:/?\\#\\[\\]@!$\'()*+,;]';
    const CLASS_ALL = '.';

    protected array $vars = [];

    /** @inheritDoc */
    public function getVars(): array
    {
        return $this->vars;
    }

    /** @inheritDoc */
    public function getRegex(string $text): string
    {
        $this->vars = [];
        $cnt = 0;

        return '#^'.preg_replace_callback(
            '#\\\\{(\\\.|;|\\\?|\\\+)?([[:alpha:]_]\w*(?:,[[:alpha:]_]\w*)*)(\\\\\*)?\\\\}#',
            function ($v) use (&$cnt) {
                $this->vars[++$cnt] = $v[2];
                $var = strtr($v[2], ',', '_');
                $explode = !empty($v[3]);
                switch ($v[1]) {
                    case '\.':
                        return sprintf(
                            '(?P<%s>\.%s+)',
                            $var,
                            $explode ? self::CLASS_UNRESERVED_EQUAL : self::CLASS_UNRESERVED_COMMA
                        );
                    case ';':
                        return sprintf(
                            '(?P<%s>;%s+)',
                            $var,
                            $explode ? self::CLASS_UNRESERVED_SEMICOLON_EQUAL : self::CLASS_UNRESERVED_COMMA_EQUAL
                        );
                    case '\?':
                        if ($explode) {
                            $vars = explode(',', $v[2]);
                            $var_pat = count($vars) > 1 ? sprintf('(?:%s)', implode('|', $vars)) : $v[2];
                            return sprintf(
                                '(?P<%s>(?:[?&]%s=%s*)+)',
                                $var,
                                $var_pat,
                                self::CLASS_UNRESERVED
                            );
                        } else {
                            return sprintf(
                                '(?P<%s>[?&]%s=%s*)',
                                $var,
                                $var,
                                self::CLASS_UNRESERVED_COMMA
                            );
                        }
                    case '\+':
                        return sprintf('(?P<%s>%s+)', $var, self::CLASS_ALL);
                    default:
                        return sprintf(
                            '(?P<%s>%s+)',
                            $var,
                            $explode ? self::CLASS_UNRESERVED_COMMA_EQUAL : self::CLASS_UNRESERVED_COMMA
                        );
                }
            },
            preg_quote($text, '#')
        ).'$#';
    }
}

<?php
declare(strict_types=1);

namespace Vertilia\Parser;

/**
 * Provides regexps to decode strings containing variables
 */
interface ParserInterface
{
    /**
     * Returns regexp that matches the text and identifies variables
     *
     * @param string $text text containing variables, ex.: "/users/{id}"
     * @return string regexp to match the text, ex.: "#^/users/(?P<id>.+)$#"
     */
    public function getRegex(string $text): string;

    /**
     * Returns the list of last extracted variables
     *
     * @return array list of variables used in last parsed string
     */
    public function getVars(): array;
}

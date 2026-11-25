<?php

declare(strict_types=1);

/*
 * This file is part of the "phpstan-typo3-dead-code" Composer package.
 *
 * (c) Konrad Michalik <hej@konradmichalik.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KonradMichalik\PhpStanTypo3DeadCode;

/**
 * Reads the property names of variable paths such as `{record.author.name}` from a Fluid template.
 */
final class FluidTemplateReader
{
    /**
     * @return list<string> every path segment after the variable name, such as `author` and `name`
     */
    public function read(string $file): array
    {
        $code = file_get_contents($file);
        if (false === $code) {
            return [];
        }

        // Innermost braces first, so `{f:if(condition: a.b, then: '{c.d}')}` keeps `a.b` once `{c.d}` is taken out.
        $blocks = [];
        do {
            $code = preg_replace_callback('/\{([^{}]*)\}/', static function (array $match) use (&$blocks): string {
                $blocks[] = $match[1];

                return ' ';
            }, $code, -1, $count) ?? '';
        } while ($count > 0);

        $names = [];
        foreach ($blocks as $block) {
            // String literals such as translation keys ('list.header') are no variable paths.
            $expression = preg_replace('/\'[^\']*\'|"[^"]*"/', ' ', $block) ?? '';
            // A segment preceded by `:`, `.` or `/` is no variable, which skips ViewHelper names such as `f:format.html`.
            preg_match_all('~(?<![\w.:/-])[a-zA-Z_]\w*((?:\.\w+)+)~', $expression, $paths);
            foreach ($paths[1] as $segments) {
                foreach (explode('.', substr($segments, 1)) as $name) {
                    $names[] = $name;
                }
            }
        }

        return array_values(array_unique($names));
    }
}

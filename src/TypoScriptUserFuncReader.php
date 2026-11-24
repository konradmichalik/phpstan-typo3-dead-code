<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode;

/**
 * Reads `userFunc = Vendor\Class->method` assignments from TypoScript files, comments excluded.
 */
final class TypoScriptUserFuncReader
{
    /**
     * @return list<array{string, string}> pairs of class name and method name
     */
    public function read(string $file): array
    {
        $code = file_get_contents($file);
        if (false === $code) {
            return [];
        }

        $code = preg_replace(['~/\*.*?\*/~s', '~^\s*(?:#|//).*$~m'], '', $code) ?? '';
        preg_match_all('/userFunc\s*=\s*\\\\?([\w\\\\]+)->(\w+)/', $code, $matches, PREG_SET_ORDER);

        return array_map(static fn (array $match): array => [$match[1], $match[2]], $matches);
    }
}

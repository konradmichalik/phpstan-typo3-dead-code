<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode;

/**
 * Reads `userFunc = Vendor\Class->method` assignments from TypoScript files.
 */
final class TypoScriptUserFuncReader
{
    /**
     * @return array<string, list<string>> class name => method names
     */
    public function read(string $file): array
    {
        $code = file_get_contents($file);
        if (false === $code) {
            return [];
        }

        preg_match_all('/userFunc\s*=\s*\\\\?([\w\\\\]+)->(\w+)/', $code, $matches, PREG_SET_ORDER);

        $functions = [];
        foreach ($matches as $match) {
            $functions[$match[1]][] = $match[2];
        }

        return $functions;
    }
}

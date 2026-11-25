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

use PhpParser\{Error, NodeTraverser, Parser, ParserFactory};
use PhpParser\NodeVisitor\NameResolver;

/**
 * Reads the methods a PHP configuration file points at: route targets, `itemsProcFunc`, `userFunc` and the like.
 */
final readonly class PhpConfigurationReader
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = (new ParserFactory())->createForNewestSupportedVersion();
    }

    /**
     * @return list<array{string, string}> pairs of class name and method name
     */
    public function read(string $file): array
    {
        $code = file_get_contents($file);
        if (false === $code) {
            return [];
        }

        try {
            $statements = $this->parser->parse($code);
        } catch (Error) {
            return [];
        }

        $collector = new TargetCollector();
        (new NodeTraverser(new NameResolver(), $collector))->traverse($statements ?? []);

        return $collector->targets;
    }
}

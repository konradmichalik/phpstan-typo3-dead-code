<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode;

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;

/**
 * Reads the methods a PHP configuration file points at: route targets, `itemsProcFunc`, `userFunc` and the like.
 */
final class PhpConfigurationReader
{
    private readonly Parser $parser;

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

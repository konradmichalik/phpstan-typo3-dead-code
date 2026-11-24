<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode;

use PhpParser\Node;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use Throwable;

/**
 * Reads route targets from Routes.php files: `Foo::class . '::bar'`, `'Fqcn::bar'`
 * and an invokable `'target' => Foo::class`.
 */
final class RouteTargetReader
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

        try {
            $statements = (new ParserFactory())->createForNewestSupportedVersion()->parse($code);
        } catch (Throwable) {
            return [];
        }
        if (null === $statements) {
            return [];
        }

        $traverser = new NodeTraverser(new NameResolver());
        $statements = $traverser->traverse($statements);

        $targets = [];
        foreach ((new NodeFinder())->find($statements, static fn (Node $node): bool => true) as $node) {
            $target = $this->targetOf($node);
            if (null !== $target) {
                $targets[$target[0]][] = $target[1];
            }
        }

        return $targets;
    }

    /**
     * @return array{string, string}|null
     */
    private function targetOf(Node $node): ?array
    {
        if ($node instanceof Concat && $node->right instanceof String_ && str_starts_with($node->right->value, '::')) {
            $class = $this->classConstant($node->left);

            return null === $class ? null : [$class, substr($node->right->value, 2)];
        }

        if ($node instanceof String_ && 1 === preg_match('/^\\\\?([\w\\\\]+)::(\w+)$/', $node->value, $match)) {
            return [$match[1], $match[2]];
        }

        if ($node instanceof ArrayItem && $node->key instanceof String_ && 'target' === $node->key->value) {
            $class = $this->classConstant($node->value);

            return null === $class ? null : [$class, '__invoke'];
        }

        return null;
    }

    private function classConstant(Node $node): ?string
    {
        if ($node instanceof ClassConstFetch
            && $node->class instanceof Name
            && $node->name instanceof Identifier
            && 'class' === $node->name->toLowerString()
        ) {
            return $node->class->toString();
        }

        return null;
    }
}

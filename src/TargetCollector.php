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
use PhpParser\NodeVisitorAbstract;

/**
 * Collects `Foo::class . '::bar'`, `Foo::class . '->bar'`, `'Vendor\Foo::bar'`, `'Vendor\Foo->bar'`
 * and an invokable `'target' => Foo::class`.
 *
 * Runs after the NameResolver and looks at a node when it is left, so its child names are resolved by then.
 */
final class TargetCollector extends NodeVisitorAbstract
{
    /** @var list<array{string, string}> */
    public array $targets = [];

    public function leaveNode(Node $node): null
    {
        $target = $this->targetOf($node);
        if (null !== $target) {
            $this->targets[] = $target;
        }

        return null;
    }

    /**
     * @return array{string, string}|null
     */
    private function targetOf(Node $node): ?array
    {
        if ($node instanceof Concat && $node->right instanceof String_ && 1 === preg_match('/^(?:::|->)(\w+)$/', $node->right->value, $match)) {
            $class = $this->classConstant($node->left);

            return null === $class ? null : [$class, $match[1]];
        }

        if ($node instanceof String_ && 1 === preg_match('/^\\\\?([\w\\\\]+)(?:::|->)(\w+)$/', $node->value, $match)) {
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

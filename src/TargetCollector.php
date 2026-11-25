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

use PhpParser\Node\{ArrayItem, Identifier, Name};
use PhpParser\Node\Expr\{ArrayDimFetch, Assign, ClassConstFetch};
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\String_;
use PhpParser\{Node, NodeVisitorAbstract};

use function in_array;

/**
 * Collects `Foo::class . '::bar'`, `Foo::class . '->bar'`, `'Vendor\Foo::bar'`, `'Vendor\Foo->bar'`,
 * an invokable `'target' => Foo::class` and DataHandler hook classes registered in `SC_OPTIONS`.
 *
 * Runs after the NameResolver and looks at a node when it is left, so its child names are resolved by then.
 */
final class TargetCollector extends NodeVisitorAbstract
{
    // Methods the DataHandler calls on a registered hook object, per hook array, as of TYPO3 13 and 14.
    private const DATA_HANDLER_HOOKS = [
        'processDatamapClass' => [
            'processDatamap_beforeStart',
            'processDatamap_preProcessFieldArray',
            'processDatamap_postProcessFieldArray',
            'processDatamap_afterDatabaseOperations',
            'processDatamap_afterAllOperations',
            'checkRecordUpdateAccess',
        ],
        'processCmdmapClass' => [
            'processCmdmap_beforeStart',
            'processCmdmap_preProcess',
            'processCmdmap',
            'processCmdmap_postProcess',
            'processCmdmap_afterFinish',
            'processCmdmap_deleteAction',
            'processCmdmap_discardAction',
            'processTranslateTo_copyAction',
        ],
        'moveRecordClass' => [
            'moveRecord',
            'moveRecord_firstElementPostProcess',
            'moveRecord_afterAnotherElementPostProcess',
        ],
        'checkModifyAccessList' => ['checkModifyAccessList'],
    ];

    /** @var list<array{string, string}> */
    public array $targets = [];

    public function leaveNode(Node $node): null
    {
        foreach ($this->targetsOf($node) as $target) {
            $this->targets[] = $target;
        }

        return null;
    }

    /**
     * @return list<array{string, string}>
     */
    private function targetsOf(Node $node): array
    {
        if ($node instanceof Concat && $node->right instanceof String_ && 1 === preg_match('/^(?:::|->)(\w+)$/', $node->right->value, $match)) {
            $class = $this->classConstant($node->left);

            return null === $class ? [] : [[$class, $match[1]]];
        }

        if ($node instanceof String_ && 1 === preg_match('/^\\\\?([\w\\\\]+)(?:::|->)(\w+)$/', $node->value, $match)) {
            return [[$match[1], $match[2]]];
        }

        if ($node instanceof ArrayItem && $node->key instanceof String_ && 'target' === $node->key->value) {
            $class = $this->classConstant($node->value);

            return null === $class ? [] : [[$class, '__invoke']];
        }

        if ($node instanceof Assign) {
            $methods = $this->dataHandlerHookMethods($node->var);
            $class = $this->classConstant($node->expr) ?? $this->className($node->expr);

            return null === $methods || null === $class
                ? []
                : array_map(static fn (string $method): array => [$class, $method], $methods);
        }

        return [];
    }

    /**
     * @return list<string>|null
     */
    private function dataHandlerHookMethods(Node $var): ?array
    {
        $keys = [];
        while ($var instanceof ArrayDimFetch) {
            if ($var->dim instanceof String_) {
                $keys[] = $var->dim->value;
            }
            $var = $var->var;
        }

        if (!in_array('t3lib/class.t3lib_tcemain.php', $keys, true)) {
            return null;
        }

        foreach ($keys as $key) {
            if (isset(self::DATA_HANDLER_HOOKS[$key])) {
                return self::DATA_HANDLER_HOOKS[$key];
            }
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

    private function className(Node $node): ?string
    {
        return $node instanceof String_ && 1 === preg_match('/^\\\\?([\w\\\\]+)$/', $node->value, $match) ? $match[1] : null;
    }
}

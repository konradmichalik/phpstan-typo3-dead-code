<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use ReflectionMethod;
use ReflectionProperty;
use ShipMonk\PHPStan\DeadCode\Enum\AccessType;
use ShipMonk\PHPStan\DeadCode\Graph\ClassMethodRef;
use ShipMonk\PHPStan\DeadCode\Graph\ClassMethodUsage;
use ShipMonk\PHPStan\DeadCode\Graph\ClassPropertyRef;
use ShipMonk\PHPStan\DeadCode\Graph\ClassPropertyUsage;
use ShipMonk\PHPStan\DeadCode\Graph\UsageOrigin;
use ShipMonk\PHPStan\DeadCode\Provider\MemberUsageProvider;

/**
 * Marks the public members of a PSR-14 event as used where it is dispatched, because listeners in other packages read them.
 */
final class EventApiProvider implements MemberUsageProvider
{
    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
        private readonly bool $enabled,
    ) {}

    public function getUsages(Node $node, Scope $scope): array
    {
        if (!$this->enabled || !$this->isDispatch($node, $scope)) {
            return [];
        }

        $origin = UsageOrigin::createRegular($node, $scope);
        $usages = [];
        foreach ($scope->getType($node->getArgs()[0]->value)->getObjectClassNames() as $className) {
            if (!$this->reflectionProvider->hasClass($className)) {
                continue;
            }

            // The dispatched type may be an interface or base class, so implementations and overrides count too.
            $event = $this->reflectionProvider->getClass($className)->getNativeReflection();
            foreach ($event->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $usages[] = new ClassMethodUsage(
                    $origin,
                    new ClassMethodRef($method->getDeclaringClass()->getName(), $method->getName(), possibleDescendant: true),
                );
            }
            foreach ($event->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                $reference = new ClassPropertyRef($property->getDeclaringClass()->getName(), $property->getName(), possibleDescendant: true);
                $usages[] = new ClassPropertyUsage($origin, $reference, AccessType::READ);
                // Mutable events hand data back: listeners write the property and the emitter reads it.
                if (!$property->isReadOnly()) {
                    $usages[] = new ClassPropertyUsage($origin, $reference, AccessType::WRITE);
                }
            }
        }

        return $usages;
    }

    /**
     * @phpstan-assert-if-true MethodCall $node
     */
    private function isDispatch(Node $node, Scope $scope): bool
    {
        return $node instanceof MethodCall
            && $node->name instanceof Identifier
            && 'dispatch' === $node->name->toLowerString()
            && !$node->isFirstClassCallable()
            && [] !== $node->getArgs()
            && (new ObjectType('Psr\EventDispatcher\EventDispatcherInterface'))->isSuperTypeOf($scope->getType($node->var))->yes();
    }
}

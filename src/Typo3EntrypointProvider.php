<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionMethod;
use ShipMonk\PHPStan\DeadCode\Provider\ReflectionBasedMemberUsageProvider;
use ShipMonk\PHPStan\DeadCode\Provider\VirtualUsageData;

/**
 * Marks methods as used that TYPO3 calls from configuration instead of PHP code.
 */
final class Typo3EntrypointProvider extends ReflectionBasedMemberUsageProvider
{
    /** @var array<string, list<string>>|null */
    private ?array $entrypoints = null;

    public function __construct(
        private readonly string $configurationDirectory,
        private readonly bool $markConstructors,
    ) {}

    protected function shouldMarkMethodAsUsed(ReflectionMethod $method): ?VirtualUsageData
    {
        if ($this->markConstructors && '__construct' === $method->getName()) {
            return VirtualUsageData::withNote('Instantiated by the TYPO3 dependency injection container');
        }

        $class = $method->getDeclaringClass()->getName();
        if (in_array($method->getName(), $this->entrypoints()[$class] ?? [], true)) {
            return VirtualUsageData::withNote('Called from TYPO3 configuration (route target or TypoScript userFunc)');
        }

        return null;
    }

    /**
     * @return array<string, list<string>>
     */
    private function entrypoints(): array
    {
        if (null !== $this->entrypoints) {
            return $this->entrypoints;
        }

        $routes = new RouteTargetReader();
        $typoScript = new TypoScriptUserFuncReader();

        $entrypoints = [];
        foreach ($this->files() as $file) {
            $found = match (true) {
                str_ends_with($file, '.typoscript') => $typoScript->read($file),
                default => $routes->read($file),
            };
            foreach ($found as $class => $methods) {
                $entrypoints[$class] = [...($entrypoints[$class] ?? []), ...$methods];
            }
        }

        return $this->entrypoints = $entrypoints;
    }

    /**
     * @return list<string>
     */
    private function files(): array
    {
        if (!is_dir($this->configurationDirectory)) {
            return [];
        }

        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->configurationDirectory, FilesystemIterator::SKIP_DOTS),
        );
        foreach ($iterator as $file) {
            $path = $file->getPathname();
            $isRoutes = 1 === preg_match('#/Backend/\w*Routes\.php$#', $path);
            if ($isRoutes || str_ends_with($path, '.typoscript')) {
                $files[] = $path;
            }
        }

        return $files;
    }
}

<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode;

use FilesystemIterator;
use PHPStan\Reflection\ReflectionProvider;
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
    /** @var array<string, true>|null "declaringclass::method", lowercase */
    private ?array $entrypoints = null;

    /**
     * @param list<string> $analysedPaths
     */
    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
        private readonly array $analysedPaths,
        private readonly string $currentWorkingDirectory,
        private readonly ?string $configurationDirectory,
        private readonly bool $markConstructors,
    ) {}

    protected function shouldMarkMethodAsUsed(ReflectionMethod $method): ?VirtualUsageData
    {
        if ($this->markConstructors && '__construct' === $method->getName()) {
            return VirtualUsageData::withNote('Instantiated by the TYPO3 dependency injection container');
        }

        // Same rule as TYPO3's AutowireInjectMethodsPass, which adds these calls to every autowired service.
        if ($method->isPublic() && (str_starts_with($method->getName(), 'inject') || 'initializeObject' === $method->getName())) {
            return VirtualUsageData::withNote('Called by the TYPO3 dependency injection container');
        }

        // Extbase resolves the action method from the request, so neither the call nor the action name is in PHP code.
        if (str_ends_with($method->getName(), 'Action') && $this->isExtbaseController($method->getDeclaringClass()->getName())) {
            return VirtualUsageData::withNote('Extbase action, dispatched by the ActionController');
        }

        if (isset($this->entrypoints()[self::key($method->getDeclaringClass()->getName(), $method->getName())])) {
            return VirtualUsageData::withNote('Called from TYPO3 configuration (route target, TCA callback, TypoScript userFunc or DataHandler hook)');
        }

        return null;
    }

    /**
     * @return array<string, true>
     */
    private function entrypoints(): array
    {
        if (null !== $this->entrypoints) {
            return $this->entrypoints;
        }

        $php = new PhpConfigurationReader();
        $typoScript = new TypoScriptUserFuncReader();

        $entrypoints = [];
        foreach ($this->files() as $file) {
            $targets = str_ends_with($file, '.typoscript') ? $typoScript->read($file) : $php->read($file);
            foreach ($targets as [$class, $method]) {
                $entrypoints[self::key($this->declaringClass($class, $method), $method)] = true;
            }
        }

        return $this->entrypoints = $entrypoints;
    }

    // PHP resolves class and method names case-insensitively, so configuration may spell them differently.
    private static function key(string $class, string $method): string
    {
        return strtolower($class . '::' . $method);
    }

    // Static reflection, because runtime autoloading fails when Extbase is only known through scanDirectories.
    private function isExtbaseController(string $class): bool
    {
        $controller = 'TYPO3\CMS\Extbase\Mvc\Controller\ActionController';

        return $this->reflectionProvider->hasClass($controller)
            && $this->reflectionProvider->hasClass($class)
            && $this->reflectionProvider->getClass($class)->isSubclassOfClass($this->reflectionProvider->getClass($controller));
    }

    // A route may name a child class while the method lives in its parent or a trait.
    private function declaringClass(string $class, string $method): string
    {
        if (!$this->reflectionProvider->hasClass($class)) {
            return $class;
        }

        $reflection = $this->reflectionProvider->getClass($class);

        return $reflection->hasNativeMethod($method)
            ? $reflection->getNativeMethod($method)->getDeclaringClass()->getName()
            : $class;
    }

    /**
     * @return list<string>
     */
    private function files(): array
    {
        $files = [];
        foreach ($this->configurationDirectories() as $directory) {
            foreach (['ext_localconf.php', 'ext_tables.php'] as $name) {
                if (is_file(dirname($directory) . '/' . $name)) {
                    $files[] = dirname($directory) . '/' . $name;
                }
            }

            if (!is_dir($directory)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            );
            foreach ($iterator as $file) {
                $path = $file->getPathname();
                if (str_ends_with($path, '.php') || str_ends_with($path, '.typoscript')) {
                    $files[] = $path;
                }
            }
        }

        return $files;
    }

    // PHPStan often runs from a subdirectory such as Tests/CGL, so the working directory says little.
    // The analysed Configuration directory is what the project already points PHPStan at.
    /**
     * @return list<string>
     */
    private function configurationDirectories(): array
    {
        if (null !== $this->configurationDirectory) {
            return [$this->configurationDirectory];
        }

        $analysed = array_values(array_filter(
            $this->analysedPaths,
            static fn (string $path): bool => 'Configuration' === basename($path) && is_dir($path),
        ));

        return [] !== $analysed ? $analysed : [$this->currentWorkingDirectory . '/Configuration'];
    }
}

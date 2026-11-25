<?php

declare(strict_types=1);

namespace KonradMichalik\PhpStanTypo3DeadCode;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Reads the methods a `Services.yaml` names: tags with `method`, such as event listeners, and factories.
 */
final class ServicesYamlReader
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

        try {
            $configuration = Yaml::parse($code, Yaml::PARSE_CUSTOM_TAGS);
        } catch (ParseException) {
            return [];
        }

        $services = is_array($configuration) ? ($configuration['services'] ?? null) : null;
        if (!is_array($services)) {
            return [];
        }

        $classes = [];
        foreach ($services as $id => $definition) {
            $classes[(string) $id] = is_array($definition) && is_string($definition['class'] ?? null) ? $definition['class'] : (string) $id;
        }

        $targets = [];
        foreach ($services as $id => $definition) {
            if (!is_array($definition)) {
                continue;
            }

            $tags = $definition['tags'] ?? null;
            foreach (is_array($tags) ? $tags : [] as $tag) {
                $method = $this->tagMethod($tag);
                if (null !== $method) {
                    $targets[] = [$classes[(string) $id], $method];
                }
            }

            $factory = $definition['factory'] ?? null;
            if (is_array($factory) && is_string($factory[0] ?? null) && is_string($factory[1] ?? null)) {
                $targets[] = [$this->factoryClass($factory[0], $classes), $factory[1]];
            }
        }

        return $targets;
    }

    // Symfony also accepts the short form `- event.listener: { method: onEvent }`.
    private function tagMethod(mixed $tag): ?string
    {
        if (is_array($tag) && 1 === count($tag) && is_array(current($tag))) {
            $tag = current($tag);
        }

        return is_array($tag) && is_string($tag['method'] ?? null) ? $tag['method'] : null;
    }

    // `@id` references a service, which may be registered under a name instead of its class. Without `@` it is a class.
    /**
     * @param array<string, string> $classes
     */
    private function factoryClass(string $reference, array $classes): string
    {
        if (!str_starts_with($reference, '@')) {
            return $reference;
        }

        $id = substr($reference, 1);

        return $classes[$id] ?? $id;
    }
}

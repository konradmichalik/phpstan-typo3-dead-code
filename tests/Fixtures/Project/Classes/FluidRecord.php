<?php

declare(strict_types=1);

namespace Fixture\Project;

final class FluidRecord
{
    public string $identifier = '';

    public string $word = '';

    public static string $tags = '';

    public function isPublished(): bool
    {
        return true;
    }

    public function getSubtitle(): string
    {
        return '';
    }

    public function getHeader(): string
    {
        return '';
    }

    public function getSummary(): string
    {
        return '';
    }

    public function getTitle(): string
    {
        return '';
    }

    public function isVisible(): bool
    {
        return true;
    }

    public function getAuthor(): object
    {
        return new \stdClass();
    }

    public function hasTags(): bool
    {
        return false;
    }

    public function getSecret(): string
    {
        return '';
    }
}

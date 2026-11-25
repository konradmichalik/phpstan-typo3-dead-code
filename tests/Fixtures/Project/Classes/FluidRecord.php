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

namespace Fixture\Project;

use stdClass;

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
        return new stdClass();
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

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

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

abstract class AbstractBlogController extends ActionController
{
    public function showAction(): void {}
}

final class BlogController extends AbstractBlogController
{
    public function initializeListAction(): void {}

    public function listAction(): void {}

    public function renderTeaser(): void {}
}

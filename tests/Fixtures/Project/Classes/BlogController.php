<?php

declare(strict_types=1);

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

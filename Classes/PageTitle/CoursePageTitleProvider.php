<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;

class CoursePageTitleProvider extends AbstractPageTitleProvider
{
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}

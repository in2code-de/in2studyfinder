<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Department extends AbstractEntity implements DepartmentInterface
{
    public const TABLE = 'tx_in2studyfinder_domain_model_department';

    protected string $title = '';

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getOptionField(): string
    {
        return $this->getTitle();
    }
}

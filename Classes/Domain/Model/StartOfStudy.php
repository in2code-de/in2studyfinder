<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class StartOfStudy extends AbstractEntity
{
    public const TABLE = 'tx_in2studyfinder_domain_model_startofstudy';

    protected string $title = '';

    protected string $startDate = '';

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function setStartDate(string $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getOptionField(): string
    {
        return $this->getTitle();
    }
}

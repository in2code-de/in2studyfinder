<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Graduation extends AbstractEntity implements GraduationInterface
{
    public const TABLE = 'tx_in2studyfinder_domain_model_graduation';

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

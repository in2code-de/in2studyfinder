<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class TypeOfStudy extends AbstractEntity
{
    public const TABLE = 'tx_in2studyfinder_domain_model_typeofstudy';

    protected string $type = '';

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getOptionField(): string
    {
        return $this->getType();
    }
}

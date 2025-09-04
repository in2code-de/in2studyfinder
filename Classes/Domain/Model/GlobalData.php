<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class GlobalData extends AbstractEntity
{
    public const TABLE = 'tx_in2studyfinder_domain_model_globaldata';

    protected string $title = '';

    protected bool $defaultPreset = false;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function isDefaultPreset(): bool
    {
        return $this->defaultPreset;
    }

    public function setDefaultPreset(bool $defaultPreset): void
    {
        $this->defaultPreset = $defaultPreset;
    }
}

<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class CourseLanguage extends AbstractEntity implements CourseLanguageInterface
{
    public const TABLE = 'tx_in2studyfinder_domain_model_courselanguage';

    protected string $language = '';

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function getOptionField(): string
    {
        return $this->getLanguage();
    }
}

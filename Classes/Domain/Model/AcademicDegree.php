<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class AcademicDegree extends AbstractEntity
{
    public const TABLE = 'tx_in2studyfinder_domain_model_academicdegree';

    protected string $degree = '';

    protected ?Graduation $graduation = null;

    public function getDegree(): string
    {
        return $this->degree;
    }

    public function setDegree(string $degree): void
    {
        $this->degree = $degree;
    }

    public function getGraduation(): ?Graduation
    {
        return $this->graduation;
    }

    public function setGraduation(Graduation $graduation): void
    {
        $this->graduation = $graduation;
    }

    public function getOptionField(): string
    {
        return $this->getDegree();
    }
}

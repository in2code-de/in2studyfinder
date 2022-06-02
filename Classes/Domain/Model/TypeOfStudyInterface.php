<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

interface TypeOfStudyInterface
{
    public function getType(): string;

    public function setType(string $type);

    public function getOptionField(): string;
}

<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

interface StartOfStudyInterface
{
    public function getTitle(): string;

    public function setTitle(string $title);

    public function getStartDate(): string;

    public function setStartDate(string $startDate);

    public function getOptionField(): string;
}

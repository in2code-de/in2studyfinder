<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Model;

interface GlobalDataInterface
{
    public function getTitle(): string;

    public function setTitle(string $title);

    public function isDefaultPreset(): bool;

    public function setDefaultPreset(bool $defaultPreset);
}

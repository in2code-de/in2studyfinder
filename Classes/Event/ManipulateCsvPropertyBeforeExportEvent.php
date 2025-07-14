<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Event;

class ManipulateCsvPropertyBeforeExportEvent
{
    public function __construct(private mixed $property)
    {
    }

    public function getProperty(): mixed
    {
        return $this->property;
    }

    public function setProperty(mixed $property): void
    {
        $this->property = $property;
    }
}

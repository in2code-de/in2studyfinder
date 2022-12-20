<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Event;

class ManipulateCsvPropertyBeforeExport
{
    private mixed $property;

    public function __construct($property)
    {
        $this->property = $property;
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

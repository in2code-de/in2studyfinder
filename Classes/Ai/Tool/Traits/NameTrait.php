<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Tool\Traits;

trait NameTrait
{
    private string $name;

    public function supports(string $name): bool
    {
        return $name === $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

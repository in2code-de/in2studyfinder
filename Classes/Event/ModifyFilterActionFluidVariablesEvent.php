<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Event;

use In2code\In2studyfinder\Controller\StudyCourseController;

final class ModifyFilterActionFluidVariablesEvent
{
    public function __construct(private readonly StudyCourseController $controller, private array $fluidVariables)
    {
    }

    public function getController(): StudyCourseController
    {
        return $this->controller;
    }

    public function getFluidVariables(): array
    {
        return $this->fluidVariables;
    }

    public function setFluidVariables(array $fluidVariables): void
    {
        $this->fluidVariables = $fluidVariables;
    }
}

<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Tool;

use In2code\In2studyfinder\Ai\Exception\MissingArgumentException;

interface ToolInterface
{
    public function getConfiguration(): array;
    public function supports(string $name): bool;
    /**
     * @throws MissingArgumentException
     */
    public function execute(array $arguments, array $pluginSettings);
    public function getName(): string;
}

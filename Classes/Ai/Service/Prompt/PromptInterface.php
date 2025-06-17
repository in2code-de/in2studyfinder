<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Service\Prompt;

interface PromptInterface
{
    public function getLocalizedPrompt(array $pluginSettings): string;
}

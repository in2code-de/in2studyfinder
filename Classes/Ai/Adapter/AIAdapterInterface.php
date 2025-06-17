<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Adapter;

use GuzzleHttp\Exception\ClientException;

interface AIAdapterInterface
{
    /**
     * @throws ClientException
     */
    public function sendMessage(string $message, array $pluginSettings): array;
}

<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Service;

use Psr\Log\LoggerInterface;

class AbstractService
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}

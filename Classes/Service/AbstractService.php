<?php

namespace In2code\In2studyfinder\Service;

use Psr\Log\LoggerInterface;

class AbstractService
{
    protected LoggerInterface $logger;

    public function injectLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}

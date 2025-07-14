<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Service;

use Psr\Log\LoggerInterface;

class AbstractService
{
    public function __construct(protected LoggerInterface $logger)
    {
    }
}

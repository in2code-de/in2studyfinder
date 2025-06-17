<?php

namespace In2code\In2studyfinder\Ai\Tool\Traits;

use In2code\In2studyfinder\Ai\Exception\MissingArgumentException;

trait GetArgumentTrait
{
    /**
     * @throws MissingArgumentException
     * @return mixed
     */
    private function getArgument(string $key, array $arguments)
    {
        $argument = $arguments[$key] ?? null;
        if ($argument === null) {
            throw new MissingArgumentException(sprintf('Expected to find argument "%s"', $key), 1749710619);
        }

        return $argument;
    }
}

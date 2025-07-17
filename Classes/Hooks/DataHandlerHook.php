<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Hooks;

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use In2code\In2studyfinder\Utility\CacheUtility;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class DataHandlerHook implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Flushes detail / filter (list) caches if a studycourse record was edited.
     */
    public function clearCachePostProc(array $params): void
    {
        if (isset($params['table']) && $params['table'] === StudyCourse::TABLE) {
            CacheUtility::flushDetailPageCache('tx_in2studyfinder_uid_' . $params['uid']);
            CacheUtility::flushListCaches('in2studyfinder');
        }
    }
}

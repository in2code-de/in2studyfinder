<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Hooks;

use In2code\In2studyfinder\Ai\Embedding\EmbeddingsConfiguration;
use In2code\In2studyfinder\Ai\Embedding\EmbeddingsHandler;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\MathUtility;

class EmbeddingsMonitor
{
    protected EmbeddingsHandler $embeddingsHandler;

    public function __construct(EmbeddingsHandler $embeddingsHandler)
    {
        $this->embeddingsHandler = $embeddingsHandler;
    }

    /**
     * Hooks into TCE main and tracks record deletion commands.
     *
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.NotCamelCaps)
     */
    public function processCmdmap_preProcess(string $command, string $table, $uid): void
    {
        if ($command === 'delete' && in_array($table, EmbeddingsConfiguration::getAllowedTables())) {
            $this->embeddingsHandler->delete($uid, $table);
        }
    }

    /**
     * Hooks into TCE Main and watches all record creations and updates. If it
     * detects that the new/updated record belongs to a table configured for
     * indexing through Solr, we add the record to the index queue.
     *
     * @inheritDoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.NotCamelCaps)
     */
    public function processDatamap_afterDatabaseOperations(
        string $status,
        string $table,
        $uid,
        array $fields,
        DataHandler $tceMain
    ): void {
        if (!in_array($table, EmbeddingsConfiguration::getAllowedTables())) {
            return;
        }

        if (MathUtility::canBeInterpretedAsInteger($uid)) {
            $record = BackendUtility::getRecord($table, $uid);
            if ($this->isEnabled($record, $table) === false) {
                return;
            }
            $this->embeddingsHandler->update($record, $table);
            return;
        }

        $uid = $tceMain->substNEWwithIDs[$uid];
        $record = BackendUtility::getRecord($table, $uid);
        if ($this->isEnabled($record, $table) === false) {
            return;
        }
        $this->embeddingsHandler->add($record, $table);
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function isEnabled(array $record, string $table): bool
    {
        $enableField = $GLOBALS['TCA'][$table]['ctrl']['enablecolumns']['disabled'];
        return boolval($record[$enableField] ?? false);
    }
}

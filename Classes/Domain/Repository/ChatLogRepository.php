<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Repository;

use Doctrine\DBAL\Exception;
use LogicException;
use PDO;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\MathUtility;

class ChatLogRepository
{
    public function __construct(protected QueryBuilder $queryBuilder, protected LoggerInterface $logger)
    {
    }

    public function create(array $logEntry): void
    {
        $this->queryBuilder->insert('tx_in2studyfinder_chat_log')
            ->values($logEntry)
            ->executeStatement();
    }

    public function findByUid(int $uid): array|null
    {
        try {
            $result = $this->queryBuilder->select('*')
                ->from('tx_in2studyfinder_chat_log')
                ->where(
                    $this->queryBuilder->expr()->eq(
                        'uid',
                        $this->queryBuilder->createNamedParameter($uid)
                    )
                )
                ->executeQuery()
                ->fetchAssociative();
        } catch (Exception $e) {
            $this->logger->error('Error fetching chat log: ' . $e->getMessage());
            return null;
        }

        return is_array($result) ? $result : null;
    }

    public function findBySessionIdentifier(string $sessionIdentifier): array|null
    {
        try {
            $result = $this->queryBuilder->select('*')
                ->from('tx_in2studyfinder_chat_log')
                ->where(
                    $this->queryBuilder->expr()->eq(
                        'session_id',
                        $this->queryBuilder->createNamedParameter($sessionIdentifier)
                    )
                )
                ->executeQuery()
                ->fetchAssociative();
        } catch (Exception $e) {
            $this->logger->error('Error fetching chat log: ' . $e->getMessage());
            return null;
        }

        return is_array($result) ? $result : null;
    }

    public function findOnPage(int $pageUid): array|null
    {
        try {
            return $this->queryBuilder->select('log.*')
                ->from('tx_in2studyfinder_chat_log', 'log')
                ->innerJoin(
                    'log',
                    'tt_content',
                    'c',
                    $this->queryBuilder->expr()->eq('c.uid', 'log.plugin_id')
                )
                ->where(
                    $this->queryBuilder->expr()->eq(
                        'c.pid',
                        $this->queryBuilder->createNamedParameter($pageUid)
                    )
                )
                ->orderBy('log.crdate', 'DESC')
                ->executeQuery()
                ->fetchAllAssociative();
        } catch (Exception $e) {
            $this->logger->error('Error fetching chat log: ' . $e->getMessage());
            return [];
        }
    }

    public function update(array $logEntry): void
    {
        $entryId = $logEntry['uid'] ?? throw new LogicException('Entry ID is missing', 1760096136);
        unset($logEntry['uid']);

        $this->queryBuilder->update('tx_in2studyfinder_chat_log')
            ->where(
                $this->queryBuilder->expr()->eq(
                    'uid',
                    $this->queryBuilder->createNamedParameter($entryId)
                )
            );

        foreach ($logEntry as $key => $value) {
            $type = Connection::PARAM_STR;
            if (MathUtility::canBeInterpretedAsInteger($value) === true) {
                $type = Connection::PARAM_INT;
            }

            $this->queryBuilder->set(
                $key,
                $this->queryBuilder->createNamedParameter($value),
                true,
                $type
            );
        }

        $this->queryBuilder->executeStatement();
    }
}

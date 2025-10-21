<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Repository;

use Doctrine\DBAL\Exception;
use LogicException;
use Psr\Log\LoggerInterface;
use Throwable;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

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

    public function findBySessionAndPluginIdentifier(string $sessionIdentifier, int $pluginUid): array|null
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
                ->andWhere(
                    $this->queryBuilder->expr()->eq(
                        'plugin_id',
                        $this->queryBuilder->createNamedParameter($pluginUid)
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

    public function updateMessages(array $logEntry): void
    {
        $entryId = $logEntry['uid'] ?? throw new LogicException('Entry ID is missing', 1760096136);
        $messages = $logEntry['messages'] ?? throw new LogicException('Messages are missing', 1760957170);

        $this->queryBuilder->update('tx_in2studyfinder_chat_log')
            ->set('messages', $messages)
            ->where(
                $this->queryBuilder->expr()->eq(
                    'uid',
                    $this->queryBuilder->createNamedParameter($entryId)
                )
            )
            ->executeStatement();
    }

    public function deleteByUid(int $uid): void
    {
        try {
            $this->queryBuilder->delete('tx_in2studyfinder_chat_log')
                ->where(
                    $this->queryBuilder->expr()->eq(
                        'uid',
                        $this->queryBuilder->createNamedParameter($uid)
                    )
                )
                ->executeStatement();
        } catch (Throwable $e) {
            $this->logger->error('Error deleting chat log: ' . $e->getMessage());
            throw $e;
        }
    }
}

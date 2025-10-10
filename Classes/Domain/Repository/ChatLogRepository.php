<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Repository;

use Doctrine\DBAL\Exception;
use LogicException;
use Psr\Log\LoggerInterface;
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
            $this->queryBuilder->set($key, $this->queryBuilder->createNamedParameter($value));
        }

        $this->queryBuilder->executeStatement();
    }
}

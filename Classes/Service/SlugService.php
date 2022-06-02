<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Service;

use In2code\In2studyfinder\Domain\Model\StudyCourse;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SlugService
{
    protected ?QueryBuilder $queryBuilder = null;

    protected array $fieldConfig = [];

    protected ?SlugHelper $slugHelper = null;

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct()
    {
        $this->fieldConfig = $GLOBALS['TCA'][StudyCourse::TABLE]['columns']['url_segment']['config'];
        $this->slugHelper =
            GeneralUtility::makeInstance(
                SlugHelper::class,
                StudyCourse::TABLE,
                'path_segment',
                $this->fieldConfig
            );

        /** @var QueryBuilder $queryBuilder */
        $this->queryBuilder =
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(StudyCourse::TABLE);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function performUpdates(): array
    {
        $this->queryBuilder->getRestrictions()->removeAll();
        $databaseQueries = [];

        $statement = $this->queryBuilder->select('*')
            ->from(StudyCourse::TABLE)
            ->where(
                $this->queryBuilder->expr()->orX(
                    $this->queryBuilder->expr()->eq(
                        'url_segment',
                        $this->queryBuilder->createNamedParameter('', \PDO::PARAM_STR)
                    ),
                    $this->queryBuilder->expr()->isNull('url_segment')
                )
            )
            ->execute();
        while ($record = $statement->fetch()) {
            if ((string)$record['title'] !== '') {
                $slug = $this->slugHelper->generate($record, (int)$record['pid']);
                $this->queryBuilder->update(StudyCourse::TABLE)
                    ->where(
                        $this->queryBuilder->expr()->eq(
                            'uid',
                            $this->queryBuilder->createNamedParameter($record['uid'], \PDO::PARAM_INT)
                        )
                    )
                    ->set('url_segment', $slug);
                $databaseQueries[] = $this->queryBuilder->getSQL();
                $this->queryBuilder->execute();
            }
        }

        return $databaseQueries;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function isSlugUpdateRequired(): bool
    {
        $this->queryBuilder->getRestrictions()->removeAll();

        $count = $this->queryBuilder->count('uid')
            ->from(StudyCourse::TABLE)
            ->where(
                $this->queryBuilder->expr()->orX(
                    $this->queryBuilder->expr()->eq(
                        'url_segment',
                        $this->queryBuilder->createNamedParameter('')
                    ),
                    $this->queryBuilder->expr()->isNull('url_segment')
                )
            )
            ->execute()->fetchColumn(0);

        return $count > 0;
    }
}

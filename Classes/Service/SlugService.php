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
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function performUpdates(): array
    {
        $queryBuilder =
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(StudyCourse::TABLE);

        $queryBuilder->getRestrictions()->removeAll();
        $databaseQueries = [];

        $records = $queryBuilder->select('*')->from(StudyCourse::TABLE)
            ->where(
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->eq(
                        'url_segment',
                        $queryBuilder->createNamedParameter('', \PDO::PARAM_STR)
                    ),
                    $queryBuilder->expr()->isNull('url_segment')
                )
            )->executeQuery()->fetchAllAssociative();

        foreach ($records as $record) {
            if ((string)$record['title'] !== '') {
                $slug = $this->slugHelper->generate($record, (int)$record['pid']);
                $queryBuilder->update(StudyCourse::TABLE)
                    ->where(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($record['uid'], \PDO::PARAM_INT)
                        )
                    )
                    ->set('url_segment', $slug);
                $databaseQueries[] = $queryBuilder->getSQL();
                $queryBuilder->executeStatement();
            }
        }

        return $databaseQueries;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function isSlugUpdateRequired(): bool
    {
        $queryBuilder =
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(StudyCourse::TABLE);

        $queryBuilder->getRestrictions()->removeAll();

        $count = $queryBuilder->count('uid')
            ->from(StudyCourse::TABLE)
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq(
                        'url_segment',
                        $queryBuilder->createNamedParameter('')
                    ),
                    $queryBuilder->expr()->isNull('url_segment')
                )
            )
            ->executeQuery()->fetchOne();

        return $count > 0;
    }
}

<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use In2code\In2studyfinder\Domain\Model\TtContent;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RecordUtility
{
    public static function getRecordWithLanguageOverlay(int $recordUid, LanguageAspect $languageAspect): array
    {
        $record = GeneralUtility::makeInstance(PageRepository::class)->getLanguageOverlay(
            TtContent::TABLE,
            self::getRecord(TtContent::TABLE, $recordUid),
            $languageAspect
        );

        if (!empty($record)) {
            return $record;
        }

        return [];
    }

    /**
     * Gets record with uid = $uid from $table
     * You can set $field to a list of fields (default is '*')
     * Additional WHERE clauses can be added by $where (fx. ' AND blabla = 1')
     * Will automatically check if records has been deleted or is disabled and if so, not return anything.
     *
     * @param string $table Table name present in $GLOBALS['TCA']
     * @param int $uid UID of record
     * @param string $fields List of fields to select
     * @param string $where Additional WHERE clause, eg. " AND blablabla = 0
     * @param bool $useDeleteClause Use the deleteClause to check if a record is deleted (default TRUE)
     * @return array|null Returns the row if found, otherwise NULL
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function getRecord(
        string $table,
        int $uid,
        string $fields = '*',
        string $where = '',
        bool $respectEnableFields = true,
        bool $useDeleteClause = true,
        int $sysLanguageUid = 0
    ): ?array {
        if ($uid > 0) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

            // do not use enabled fields here
            if (!$respectEnableFields) {
                $queryBuilder->getRestrictions()->removeAll();
            }

            // should the delete clause be used
            if ($useDeleteClause) {
                /** @var DeletedRestriction $deletedRestriction */
                $deletedRestriction = GeneralUtility::makeInstance(DeletedRestriction::class);
                $queryBuilder->getRestrictions()->add($deletedRestriction);
            }

            $queryBuilder
                ->select(...GeneralUtility::trimExplode(',', $fields, true))
                ->from($table);

            // set where clause
            if ($sysLanguageUid === 0) {
                $queryBuilder->where(
                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid))
                );
            } else {
                $queryBuilder->where(
                    $queryBuilder->expr()->eq('l18n_parent', $queryBuilder->createNamedParameter($uid)),
                    $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($sysLanguageUid))
                );
            }

            // add custom where clause
            if ($where !== '' && $where !== '0') {
                $queryBuilder->andWhere(QueryHelper::stripLogicalOperatorPrefix($where));
            }

            $row = $queryBuilder->executeQuery()->fetchAssociative();
            if ($row) {
                return $row;
            }
        }

        return null;
    }
}

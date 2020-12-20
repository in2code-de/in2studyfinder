<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use In2code\In2studyfinder\Domain\Model\TtContent;
use TYPO3\CMS\Core\Database\Query\QueryHelper;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RecordUtility extends AbstractUtility
{
    /**
     * gets and tt_content record with all translations. The array key represents the sys_language_uid of the record.
     *
     * e.g.
     *
     * 0 => [
     *      'uid' => 123,
     *      'sys_language_uid' => 0,
     *      ...
     * ],
     * 1 => [
     *      'uid' => 124,
     *      'sys_language_uid' => 1,
     *      ...
     * ]
     *
     * @param int $uid
     * @return array
     */
    public static function getRecordWithTranslations(int $uid): array
    {
        $records = [];
        $record = self::getRecord(TtContent::TABLE, $uid);

        if ($record['l18n_parent'] !== 0) {
            $records[0] = self::getRecord(TtContent::TABLE, (int)$record['l18n_parent']);
            $records[(int)$record['sys_language_uid']] = $record;
        } else {
            $records[0] = $record;
        }

        $queryBuilder = self::getQueryBuilderForTable(TtContent::TABLE);
        $translatedRecords = $queryBuilder
            ->select('*')
            ->from(TtContent::TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'l18n_parent',
                    $queryBuilder->createNamedParameter((int)$records[0]['uid'], \PDO::PARAM_INT)
                )
            )->execute()->fetchAll();

        foreach ($translatedRecords as $translatedRecord) {
            if (!array_key_exists((int)$translatedRecord['sys_language_uid'], $records)) {
                $records[(int)$translatedRecord['sys_language_uid']] = $translatedRecord;
            }
        }

        return $records;
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
     * @param bool $respectEnableFields
     * @param bool $useDeleteClause Use the deleteClause to check if a record is deleted (default TRUE)
     * @param int $sysLanguageUid
     * @return array|null Returns the row if found, otherwise NULL
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
        if ((int)$uid) {
            $queryBuilder = self::getQueryBuilderForTable($table);

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
            if ($where) {
                $queryBuilder->andWhere(QueryHelper::stripLogicalOperatorPrefix($where));
            }

            $row = $queryBuilder->execute()->fetch();
            if ($row) {
                return $row;
            }
        }
        return null;
    }
}

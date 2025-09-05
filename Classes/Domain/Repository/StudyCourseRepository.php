<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Domain\Repository;

use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class StudyCourseRepository extends AbstractRepository
{
    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
    ];

    public function findAllFilteredByOptions($options): QueryResultInterface
    {
        $query = $this->createQuery();

        if (!empty($options['storagePids'])) {
            $storagePids = $options['storagePids'];
        } else {
            $storagePids = $query->getQuerySettings()->getStoragePageIds();
        }

        unset($options['storagePids']);

        $settings = ExtensionUtility::getExtensionSettings('in2studyfinder');

        /**
         * add settings pid
         */
        $settingsPid = (int)($settings['settingsPid'] ?? 0);
        if (!in_array($settingsPid, $storagePids)) {
            $storagePids[] = $settingsPid;
        }

        $query->getQuerySettings()->setStoragePageIds($storagePids);

        $constraints = [];
        foreach ($options as $name => $array) {
            if ($array[0] === 'true') {
                $constraints[] = $query->logicalOr(
                    $query->logicalNot($query->equals($name, '')),
                    $query->greaterThan($name, 0),
                );
            } elseif ($array[0] === 'false') {
                $constraints[] = $query->logicalOr(
                    $query->equals($name, 0),
                    $query->equals($name, ''),
                    $query->equals($name, null),
                );
            } else {
                $constraints[] = $query->in($name . '.uid', $array);
            }
        }

        if (!empty($constraints)) {
            $query->matching($query->logicalAnd(...$constraints));
        }

        return $query->execute();
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function findAllForExport(
        int $sysLanguageUid = 0,
        bool $includeDeleted = false,
        bool $ignoreEnableFields = false
    ): array {
        $query = $this->createQuery();
        $constraints = [];

        $query->getQuerySettings()->setRespectStoragePage(false);

        if ($sysLanguageUid > 0) {
            $query->getQuerySettings()->setRespectSysLanguage(false);
            $constraints[] = $query->equals('sysLanguageUid', $sysLanguageUid);
        }

        if ($includeDeleted) {
            $query->getQuerySettings()->setIncludeDeleted(true);
        }

        if ($ignoreEnableFields) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);
        }

        if (!empty($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        return $query->execute()->toArray();
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findByUidsAndLanguage(array $uids, int $sysLanguageUid): QueryResultInterface
    {
        $constraints = [];
        $query = $this->createQuery();

        if ($sysLanguageUid === 0) {
            $constraints[] = $query->in('uid', $uids);
        } else {
            $query->getQuerySettings()->setRespectSysLanguage(false);

            $constraints[] = $query->in('l10nParent', $uids);
            $constraints[] = $query->equals('sysLanguageUid', $sysLanguageUid);
        }

        $query->matching($query->logicalAnd(...$constraints));

        return $query->execute();
    }
}

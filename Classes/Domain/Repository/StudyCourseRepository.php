<?php

namespace In2code\In2studyfinder\Domain\Repository;

use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * The repository for StudyCourses
 */
class StudyCourseRepository extends AbstractRepository
{
    protected $defaultOrderings = [
        'title' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * @param $options
     * @return array|QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findAllFilteredByOptions($options)
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
        if (!in_array((int)$settings['settingsPid'], $storagePids)) {
            $storagePids[] = (int)$settings['settingsPid'];
        }

        $query->getQuerySettings()->setStoragePageIds($storagePids);

        $constraints = [];
        foreach ($options as $name => $array) {
            if ($array[0] === 'true') {
                $constraints[] = $query->logicalOr(
                    [
                        $query->logicalNot($query->equals($name, '')),
                        $query->greaterThan($name, 0),
                    ]
                );
            } elseif ($array[0] === 'false') {
                $constraints[] = $query->logicalOr(
                    [
                        $query->equals($name, 0),
                        $query->equals($name, ''),
                        $query->equals($name, null),
                    ]
                );
            } else {
                $constraints[] = $query->in($name . '.uid', $array);
            }
        }

        if (!empty($constraints)) {
            $query->matching($query->logicalAnd($constraints));
        }

        return $query->execute();
    }

    /**
     * finds courses for the given parameters
     *
     * @param bool $includeDeleted
     * @param bool $ignoreEnableFields
     * @return array|QueryResultInterface
     */
    public function findAllForExport($includeDeleted = false, $ignoreEnableFields = false)
    {
        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        if ($includeDeleted) {
            $query->getQuerySettings()->setIncludeDeleted(true);
        }

        if ($ignoreEnableFields) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);
        }

        return $query->execute();
    }

    /**
     * @param array $uids
     * @param int $sysLanguageUid
     * @return array|QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findByUidsAndLanguage(array $uids, int $sysLanguageUid)
    {
        $query = $this->createQuery();

        if ($sysLanguageUid === 0) {
            $constraints[] = $query->in('uid', $uids);
        } else {
            $query->getQuerySettings()->setRespectSysLanguage(false);

            $constraints[] = $query->in('l10nParent', $uids);
            $constraints[] = $query->equals('sysLanguageUid', $sysLanguageUid);
        }

        $query->matching($query->logicalAnd($constraints));

        return $query->execute();
    }
}

<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Slug;

use In2code\In2studyfinder\Domain\Model\AcademicDegree;
use In2code\In2studyfinder\Domain\Model\Graduation;
use In2code\In2studyfinder\Domain\Model\StudyCourse;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class UrlSegmentPostModifier
 */
class UrlSegmentPostModifier
{
    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @param array $configuration
     * @param SlugHelper $slugHelper
     * @return string
     * @noinspection PhpUnusedParameterInspection
     * @noinspection PhpUnused
     */
    public function extendWithGraduation(array $configuration, SlugHelper $slugHelper): string
    {
        $this->configuration = $configuration;
        return $configuration['slug'] . '-' . $this->getGraduationTitle();
    }

    /**
     * @return string
     */
    protected function getGraduationTitle(): string
    {
        $queryBuilder =
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(StudyCourse::TABLE);

        return (string)$queryBuilder->select(Graduation::TABLE . '.title')
            ->from(StudyCourse::TABLE)
            ->leftJoin(
                StudyCourse::TABLE,
                AcademicDegree::TABLE,
                AcademicDegree::TABLE,
                $queryBuilder->expr()->eq(
                    StudyCourse::TABLE . '.academic_degree',
                    $queryBuilder->quoteIdentifier(AcademicDegree::TABLE . '.uid')
                )
            )
            ->leftJoin(
                StudyCourse::TABLE,
                Graduation::TABLE,
                Graduation::TABLE,
                $queryBuilder->expr()->eq(
                    AcademicDegree::TABLE . '.graduation',
                    $queryBuilder->quoteIdentifier(Graduation::TABLE . '.uid')
                )
            )
            ->where($queryBuilder->expr()->eq(StudyCourse::TABLE . '.uid', $this->getStudyCourseRecordIdentifier()))
            ->execute()->fetchColumn();
    }

    /**
     * @return int
     */
    protected function getStudyCourseRecordIdentifier(): int
    {
        if (!empty($this->configuration['record']['uid'])) {
            $identifier = $this->configuration['record']['uid'];
        } elseif ((int)GeneralUtility::_GP('recordId') > 0) {
            $identifier = (int)GeneralUtility::_GP('recordId');
        } else {
            throw new \LogicException('No record identifier given', 1585056768);
        }
        return $identifier;
    }
}

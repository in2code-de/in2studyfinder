<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Slug;

use In2code\In2studyfinder\Domain\Model\AcademicDegree;
use In2code\In2studyfinder\Domain\Model\Graduation;
use In2code\In2studyfinder\Domain\Model\StudyCourse;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

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
        $graduationTitle = '';

        if ($this->isNewRecord()) {
            if (!empty($this->configuration['record']['academic_degree'])) {
                $graduationTitle =
                    $this->getGraduationTitle((int)$this->configuration['record']['academic_degree']);
            }
        } else {
            $graduationTitle = $this->getGraduationTitle();
        }

        if (!empty($graduationTitle)) {
            $slug = $configuration['slug'] . '-' . $graduationTitle;
        } else {
            $slug = $configuration['slug'];
        }

        return $slug;
    }

    /**
     * @param int $academicDegreeUid
     * @return string
     */
    protected function getGraduationTitle(int $academicDegreeUid = -1): string
    {
        $queryBuilder =
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(StudyCourse::TABLE);

        $queryBuilder->select(Graduation::TABLE . '.title');

        if ($academicDegreeUid > 0) {
            $queryBuilder
                ->from(Graduation::TABLE)
                ->leftJoin(
                    Graduation::TABLE,
                    AcademicDegree::TABLE,
                    AcademicDegree::TABLE,
                    $queryBuilder->expr()->eq(
                        Graduation::TABLE . '.uid',
                        AcademicDegree::TABLE . '.graduation'
                    )
                )
                ->where($queryBuilder->expr()->eq(AcademicDegree::TABLE . '.uid', $academicDegreeUid));
        } else {
            $queryBuilder->from(StudyCourse::TABLE)
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
                ->where(
                    $queryBuilder->expr()->eq(StudyCourse::TABLE . '.uid', $this->getStudyCourseRecordIdentifier())
                );
        }

        return (string)$queryBuilder->execute()->fetchColumn();
    }

    /**
     * @return bool
     */
    protected function isNewRecord(): bool
    {
        if ($this->isRecalculateSlug()) {
            $recordUid = GeneralUtility::_GP('recordId');

            if (!MathUtility::canBeInterpretedAsInteger($recordUid)) {
                return true;
            }
        } else {
            $data = GeneralUtility::_GP('data');
            if (key($data) === StudyCourse::TABLE) {
                return true;
            }
        }

        return false;
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

    /**
     * @return bool
     */
    protected function isRecalculateSlug(): bool
    {
        if (GeneralUtility::_GP('route') === '/ajax/record/slug/suggest') {
            return true;
        }

        return false;
    }
}

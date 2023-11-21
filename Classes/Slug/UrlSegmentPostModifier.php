<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Slug;

use In2code\In2studyfinder\Domain\Model\AcademicDegree;
use In2code\In2studyfinder\Domain\Model\Graduation;
use In2code\In2studyfinder\Domain\Model\StudyCourse;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class UrlSegmentPostModifier
{
    protected array $configuration = [];

    /**
     * @noinspection PhpUnusedParameterInspection
     * @noinspection PhpUnused
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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

    protected function isNewRecord(): bool
    {
        return $this->isRecalculateSlug() &&
            !MathUtility::canBeInterpretedAsInteger($this->getRequest()->getParsedBody()['recordId']);
    }

    protected function getStudyCourseRecordIdentifier(): int
    {
        $identifier = $this->getRequest()->getParsedBody()['recordId'];
        if (!MathUtility::canBeInterpretedAsInteger($identifier)) {
            throw new LogicException('No record identifier given', 1585056768);
        }

        return (int)$identifier;
    }

    protected function isRecalculateSlug(): bool
    {
        return $this->getRequest()->getAttribute('route')->getPath() === '/ajax/record/slug/suggest';
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}

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
    public function __construct(private readonly \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool)
    {
    }

    protected int $courseId = -1;

    protected int $academicDegree = -1;

    /**
     * @noinspection PhpUnusedParameterInspection
     * @noinspection PhpUnused
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function extendWithGraduation(array $configuration, SlugHelper $slugHelper): string
    {
        $this->configuration = $configuration;

        if (!$this->isUpgradeWizard() && !$this->isNewRecord()) {
            $this->courseId = $this->getStudyCourseRecordIdentifier();
        }

        if (!empty($this->configuration['record']['academic_degree'])) {
            $this->academicDegree = (int)$this->configuration['record']['academic_degree'];
        }

        $graduationTitle = $this->getGraduationTitle();

        if ($graduationTitle !== '') {
            return $configuration['slug'] . '-' . $graduationTitle;
        }

        return $configuration['slug'];
    }

    protected function getGraduationTitle(): string
    {
        $queryBuilder =
            $this->connectionPool->getQueryBuilderForTable(StudyCourse::TABLE);

        $queryBuilder->select(Graduation::TABLE . '.title');

        if ($this->academicDegree > 0) {
            return (string)$queryBuilder
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
                ->where(
                    $queryBuilder->expr()->eq(AcademicDegree::TABLE . '.uid',
                        $this->academicDegree
                    )
                )->executeQuery()->fetchOne();
        }

        if ($this->courseId > 0) {
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
                    $queryBuilder->expr()->eq(StudyCourse::TABLE . '.uid', $this->courseId)
                );

            return (string)$queryBuilder->executeQuery()->fetchOne();
        }

        return '';
    }

    private function isUpgradeWizard(): bool
    {
        // call via cli
        if (http_response_code() === false) {
            return true;
        }

        return !is_null($GLOBALS['TYPO3_REQUEST']->getParsedBody()['install'] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()['install'] ?? null) &&
            array_key_exists('action', $GLOBALS['TYPO3_REQUEST']->getParsedBody()['install'] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()['install'] ?? null) &&
            ($GLOBALS['TYPO3_REQUEST']->getParsedBody()['install'] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()['install'] ?? null)['action'] === 'upgradeWizardsExecute';
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

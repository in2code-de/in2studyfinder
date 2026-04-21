<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Slug;

use In2code\In2studyfinder\Domain\Model\AcademicDegree;
use In2code\In2studyfinder\Domain\Model\Graduation;
use In2code\In2studyfinder\Domain\Model\StudyCourse;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class UrlSegmentPostModifier
{
    protected array $configuration = [];
    protected int $courseId = -1;
    protected int $academicDegree = -1;

    public function __construct(private readonly ConnectionPool $connectionPool)
    {
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @noinspection PhpUnused
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function extendWithGraduation(array $configuration, SlugHelper $slugHelper): string
    {
        $this->configuration = $configuration;

        if (!$this->isUpgradeWizard() && !$this->isNewRecord()) {
            $this->courseId = $this->resolveStudyCourseRecordIdentifier();
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
                    $queryBuilder->expr()->eq(
                        AcademicDegree::TABLE . '.uid',
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
        if (PHP_SAPI === 'cli') {
            return true;
        }

        $request = $this->getRequest();
        $installParams = $request->getParsedBody()['install'] ?? $request->getQueryParams()['install'] ?? null;
        return is_array($installParams) && ($installParams['action'] ?? null) === 'upgradeWizardsExecute';
    }

    protected function isNewRecord(): bool
    {
        return $this->resolveStudyCourseRecordIdentifier() <= 0;
    }

    private function resolveStudyCourseRecordIdentifier(): int
    {
        $parsedBodyId = $this->getRequest()->getParsedBody()['recordId'] ?? null;
        if (MathUtility::canBeInterpretedAsInteger($parsedBodyId) && (int)$parsedBodyId > 0) {
            return (int)$parsedBodyId;
        }

        $uid = $this->configuration['record']['uid'] ?? null;
        if (MathUtility::canBeInterpretedAsInteger($uid) && (int)$uid > 0) {
            return (int)$uid;
        }

        $editParams = $this->getRequest()->getQueryParams()['edit'][StudyCourse::TABLE] ?? [];
        foreach ($editParams as $recordId => $action) {
            if ($action !== 'new' && MathUtility::canBeInterpretedAsInteger($recordId) && (int)$recordId > 0) {
                return (int)$recordId;
            }
        }

        return 0;
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}

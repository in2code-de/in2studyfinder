<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Updates;

use In2code\In2studyfinder\Service\SlugService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class StudyCourseSlugUpdater implements UpgradeWizardInterface
{
    protected ?SlugService $slugService = null;

    public function __construct()
    {
        $this->slugService = GeneralUtility::makeInstance(SlugService::class);
    }

    public function getIdentifier(): string
    {
        return 'studyCourseSlug';
    }

    #[\Override]
    public function getTitle(): string
    {
        return 'Updates slug field "path_segment" of EXT:in2studyfinder records';
    }

    #[\Override]
    public function getDescription(): string
    {
        return 'Fills empty slug field "path_segment" of EXT:in2studyfinder records with urlized title and graduation.';
    }

    #[\Override]
    public function executeUpdate(): bool
    {
        $this->slugService->performUpdates();
        return true;
    }

    #[\Override]
    public function updateNecessary(): bool
    {
        return $this->slugService->isSlugUpdateRequired();
    }

    #[\Override]
    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class
        ];
    }
}

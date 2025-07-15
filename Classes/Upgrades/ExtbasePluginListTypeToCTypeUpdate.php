<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Upgrades;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;

#[UpgradeWizard('in2studyfinderPluginListTypeToCTypeUpdate')]
final class ExtbasePluginListTypeToCTypeUpdate extends \TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate
{
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'in2studyfinder_pi1' => 'in2studyfinder_filter',
            'in2studyfinder_pi2' => 'in2studyfinder_detail',
            'in2studyfinder_fastSearch' => 'in2studyfinder_fastSearch',
        ];
    }

    public function getTitle(): string
    {
        return 'Migrates in2studyfinder plugins';
    }

    public function getDescription(): string
    {
        return 'Migrates in2studyfinder_pi1, in2studyfinder_pi2, in2studyfinder_FastSearch from list_type to CType.';
    }
}

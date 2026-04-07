<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Upgrades;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;

#[UpgradeWizard('in2studyfinderPluginListTypeToCTypeUpdate')]
final class ExtbasePluginListTypeToCTypeUpdate extends \TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate
{
    #[\Override]
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'in2studyfinder_pi1' => 'in2studyfinder_filter',
            'in2studyfinder_pi2' => 'in2studyfinder_detail',
            'in2studyfinder_fastsearch' => 'in2studyfinder_fastsearch',
        ];
    }

    #[\Override]
    public function getTitle(): string
    {
        return 'Migrates in2studyfinder plugins';
    }

    #[\Override]
    public function getDescription(): string
    {
        return 'Migrates in2studyfinder_pi1, in2studyfinder_pi2, in2studyfinder_fastsearch from list_type to CType.';
    }
}

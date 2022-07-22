<?php

$ll = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';
$table = \In2code\In2studyfinder\Domain\Model\GlobalData::TABLE;
$icon =
    TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName(
        'EXT:in2studyfinder/Resources/Public/Icons/' . $table . '.png'
    );

if (In2code\In2studyfinder\Utility\ConfigurationUtility::isEnableGlobalData()) {
    return [
        'ctrl' => [
            'title' => $ll . 'globalData',
            'label' => 'title',
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'cruser_id' => 'cruser_id',
            'dividers2tabs' => true,
            'versioningWS' => 2,
            'versioning_followPages' => true,
            'languageField' => 'sys_language_uid',
            'transOrigPointerField' => 'l10n_parent',
            'transOrigDiffSourceField' => 'l10n_diffsource',
            'delete' => 'deleted',
            'enablecolumns' => [
                'disabled' => 'hidden',
                'starttime' => 'starttime',
                'endtime' => 'endtime',
            ],
            'searchFields' => 'title,',
            'iconfile' => $icon,
        ],
        'types' => [
            '0' => [
                'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, default_preset;;1, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime',
            ],
        ],
        'palettes' => [
            '1' => ['showitem' => ''],
        ],
        'columns' => [

            'sys_language_uid' => [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
                'config' => [
                    'type' => 'language',
                ],
            ],
            'l10n_parent' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForL10nParent($table),
            'l10n_diffsource' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForL10nDiffsource(),
            't3ver_label' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForT3verLabel(),
            'hidden' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForHidden(),
            'starttime' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForStartTime(),
            'endtime' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForEndTime(),
            'title' => [
                'exclude' => 1,
                'label' => $ll . 'title',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim,required',
                    'max' => 255,
                ],
            ],
            'default_preset' => [
                'exclude' => 1,
                'label' => $ll . 'defaultPreset',
                'config' => [
                    'type' => 'check',
                    'default' => 0,
                    'eval' => 'maximumRecordsChecked',
                    'validation' => [
                        'maximumRecordsChecked' => 1,
                    ],
                ],
            ],
        ],
    ];
}

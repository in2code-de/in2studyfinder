<?php

use In2code\In2studyfinder\Utility\ConfigurationUtility;
use In2code\In2studyfinder\Utility\TcaUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$ll = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';
$table = 'tx_in2studyfinder_domain_model_globaldata';
$icon = ExtensionManagementUtility::extRelPath('in2studyfinder') . 'Resources/Public/Icons/' . $table . '.png';

if (ConfigurationUtility::isEnableGlobalData()) {
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
            'iconfile' => $icon
        ],
        'interface' => [
            'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, default_preset',
        ],
        'types' => [
            '0' => [
                'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, default_preset;;1,' . '--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'
            ],
        ],
        'palettes' => [
            '1' => ['showitem' => ''],
        ],
        'columns' => [

            'sys_language_uid' => TcaUtility::getFullTcaForSysLanguageUid(),
            'l10n_parent' => TcaUtility::getFullTcaForL10nParent($table),
            'l10n_diffsource' => TcaUtility::getFullTcaForL10nDiffsource(),
            't3ver_label' => TcaUtility::getFullTcaForT3verLabel(),
            'hidden' => TcaUtility::getFullTcaForHidden(),
            'starttime' => TcaUtility::getFullTcaForStartTime(),
            'endtime' => TcaUtility::getFullTcaForEndTime(),
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
                        'maximumRecordsChecked' => 1
                    ]
                ],
            ],
        ],
    ];
}

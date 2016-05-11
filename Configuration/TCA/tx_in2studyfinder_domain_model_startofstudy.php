<?php
use In2code\In2studyfinder\Utility\TcaGenerator;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$ll = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';
$table = 'tx_in2studyfinder_domain_model_startofstudy';
$icon = ExtensionManagementUtility::extRelPath('in2studyfinder') . 'Resources/Public/Icons/' . $table . '.png';

return [
    'ctrl' => [
        'title' => $ll . $table,
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
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, start_date',
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, start_date, ' .
                '--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [

        'sys_language_uid' => TcaGenerator::getFullTcaForSysLanguageUid(),
        'l10n_parent' => TcaGenerator::getFullTcaForL10nParent($table),
        'l10n_diffsource' => TcaGenerator::getFullTcaForL10nDiffsource(),
        't3ver_label' => TcaGenerator::getFullTcaForT3verLabel(),
        'hidden' => TcaGenerator::getFullTcaForHidden(),
        'starttime' => TcaGenerator::getFullTcaForStartTime(),
        'endtime' => TcaGenerator::getFullTcaForEndTime(),
        'title' => [
            'exclude' => 1,
            'label' => $ll . $table . '.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'start_date' => [
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => 'Start Zeitpunkt',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'date',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
    ],
];

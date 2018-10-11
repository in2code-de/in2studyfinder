<?php

$ll = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';
$table = 'tx_in2studyfinder_domain_model_academicdegree';
$icon =
    TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName(
        'EXT:in2studyfinder/Resources/Public/Icons' . $table . '.png'
    );

return [
    'ctrl' => [
        'title' => $ll . 'academicDegree',
        'label' => 'degree',
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
        'searchFields' => 'degree,',
        'iconfile' => $icon,
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, degree, graduation',
    ],
    'types' => [
        '0' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, degree, graduation, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime',
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [

        'sys_language_uid' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForSysLanguageUid(),
        'l10n_parent' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForL10nParent($table),
        'l10n_diffsource' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForL10nDiffsource(),
        't3ver_label' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForT3verLabel(),
        'hidden' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForHidden(),
        'starttime' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForStartTime(),
        'endtime' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForEndTime(),
        'degree' => [
            'exclude' => 1,
            'label' => $ll . 'academicDegree.degree',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'max' => 255,
            ],
        ],
        'graduation' => [
            'exclude' => 1,
            'label' => $ll . 'graduation.title',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_in2studyfinder_domain_model_graduation',
                'items' => [
                    In2code\In2studyfinder\Utility\TcaUtility::getPleaseChooseOption(
                        'tx_in2studyfinder_domain_model_graduation'
                    )
                ],
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
    ],
];

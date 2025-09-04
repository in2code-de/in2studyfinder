<?php

use In2code\In2studyfinder\Domain\Model\StartOfStudy;

$ll = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => $ll . 'startOfStudy',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'languageField' => 'sys_language_uid',
        'translationSource' => 'l10n_source',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title,',
        'iconfile' => 'EXT:in2studyfinder/Resources/Public/Icons/' . StartOfStudy::TABLE . '.png',
    ],
    'types' => [
        '0' => [
            'showitem' => 'title, start_date,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access,
            ',
        ],
    ],
    'palettes' => [
        'hidden' => [
            'showitem' => 'hidden',
        ],
        'language' => [
            'showitem' => 'sys_language_uid,l18n_parent',
        ],
        'access' => [
            'label' => 'LLL:EXT:frontend/Resources/private/Language/locallang_ttc.xlf:palette.access',
            'showitem' => 'starttime,endtime,--linebreak--,fe_group',
        ],
    ],
    'columns' => [
        'title' => [
            'exclude' => true,
            'label' => $ll . 'title',
            'l10n_mode' => 'prefixLangTitle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'required' => true,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'start_date' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'startOfStudy.startDate',
            'config' => [
                'type' => 'input',
                'renderType' => 'datetime',
                'default' => 0,
                'eval' => 'date,int',
            ],
        ],
    ],
];


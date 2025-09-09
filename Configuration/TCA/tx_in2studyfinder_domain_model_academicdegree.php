<?php

use In2code\In2studyfinder\Domain\Model\AcademicDegree;
use In2code\In2studyfinder\Domain\Model\Graduation;

$ll = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => $ll . 'academicDegree',
        'label' => 'degree',
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
        'searchFields' => 'degree,',
        'iconfile' => 'EXT:in2studyfinder/Resources/Public/Icons/' . AcademicDegree::TABLE . '.png',
    ],
    'types' => [
        '0' => [
            'showitem' => 'degree, graduation,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,--palette--;;hidden,--palette--;;access,',
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
        'degree' => [
            'exclude' => true,
            'l10n_mode' => 'prefixLangTitle',
            'label' => $ll . 'academicDegree.degree',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'required' => true,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'graduation' => [
            'exclude' => true,
            'label' => $ll . 'graduation.title',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tca.select.please_choose',
                        'value' => 0,
                        'icon' => 'EXT:in2studyfinder/Resources/Public/Icons/' . Graduation::TABLE . '.png'
                    ],
                ],
                'foreign_table' => Graduation::TABLE,
                'foreign_table_where' => 'AND ' . Graduation::TABLE . '.sys_language_uid in (-1, 0)',
                'default' => 0,
                'minitems' => 1,
            ],
        ],
    ],
];

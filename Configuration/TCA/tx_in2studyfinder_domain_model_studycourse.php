<?php
use In2code\In2studyfinder\Utility\TcaUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$ll = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';
$table = 'tx_in2studyfinder_domain_model_studycourse';
$icon = ExtensionManagementUtility::extRelPath('in2studyfinder') . 'Resources/Public/Icons/' . $table . '.png';

return [
    'ctrl' => [
        'title' => $ll . 'studycourse',
        'label' => 'title',
        'label_alt' => 'academic_degree',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'sortby' => 'sorting',
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'requestUpdate' => 'different_preset',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title,standard_period_of_study,ects_credits,tuition_fee,teaser,description,' .
            'university_place,content_elements,academic_degree,department,faculty,types_of_study,course_languages,' .
            'admission_requirements,starts_of_study,',
        'iconfile' => $icon
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, ' .
            'standard_period_of_study, ects_credits, teaser, description, tuition_fee, university_place, ' .
            'content_elements, academic_degree, department, faculty, types_of_study, course_languages, ' .
            'admission_requirements, starts_of_study, meta_pagetitles, meta_keywordss, meta_description,different_preset,global_data_preset',
    ],
    'types' => [
        '0' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, ' .
                '--palette--;' . $ll . 'keyData;keyData,' .
                'teaser;;;richtext:rte_transform[mode=ts_links], description;;;richtext:rte_transform[mode=ts_links], '
                . 'content_elements, ' .
                '--div--;' . $ll . 'metadata, --palette--;' . $ll . 'metadata;metadata, ' .
                '--div--;' . $ll . 'globalPreset, --palette--;' . $ll . 'globalPreset;globalPreset, ' .
                '--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime,'
        ],
    ],
    'palettes' => [
        'keyData' => [
            'showitem' => 'academic_degree, --linebreak--, course_languages, --linebreak--, types_of_study,' .
                ' --linebreak--, admission_requirements, --linebreak--,  starts_of_study, --linebreak--,' .
                ' ects_credits, --linebreak--, tuition_fee, standard_period_of_study, --linebreak--, ' .
                'university_place, faculty, --linebreak--, department',
            'canNotCollapse' => 1,
        ],
        'metadata' => [
            'showitem' => 'meta_pagetitle, --linebreak--, meta_keywords, --linebreak--, meta_description',
            'canNotCollapse' => 1,
        ],
        'globalPreset' => [
            'showitem' => 'different_preset, --linebreak--, global_data_preset',
            'canNotCollapse' => 1,
        ]
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
        'standard_period_of_study' => [
            'exclude' => 1,
            'label' => $ll . 'standardPeriodOfStudy',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'ects_credits' => [
            'exclude' => 1,
            'label' => $ll . 'ectsCredits',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'tuition_fee' => [
            'exclude' => 1,
            'label' => $ll . 'tuitionFee',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'double2'
            ]
        ],
        'teaser' => [
            'exclude' => 1,
            'label' => $ll . 'teaser',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim,required',
                'wizards' => [
                    'RTE' => [
                        'icon' => 'wizard_rte2.gif',
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'module' => [
                            'name' => 'wizard_rich_text_editor',
                            'urlParameters' => [
                                'mode' => 'wizard',
                                'act' => 'wizard_rte.php'
                            ]
                        ],
                        'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
                        'type' => 'script'
                    ]
                ]
            ],
        ],
        'description' => [
            'exclude' => 1,
            'label' => $ll . 'description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'wizards' => [
                    'RTE' => [
                        'icon' => 'wizard_rte2.gif',
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'module' => [
                            'name' => 'wizard_rich_text_editor',
                            'urlParameters' => [
                                'mode' => 'wizard',
                                'act' => 'wizard_rte.php'
                            ]
                        ],
                        'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
                        'type' => 'script'
                    ]
                ]
            ],
        ],
        'university_place' => [
            'exclude' => 1,
            'label' => $ll . 'universityPlace',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'content_elements' => [
            'exclude' => 1,
            'label' => $ll . 'contentElements',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tt_content',
                'foreign_table' => 'tt_content',
                'MM' => 'tx_in2studyfinder_studycourse_ttcontent_mm',
                'maxitems' => 9999,
                'size' => 10,
                'wizards' => TcaUtility::getSuggestWizard(),
            ],
        ],
        'academic_degree' => TcaUtility::getFullTcaForSingleSelect(
            $ll . 'academicDegree',
            'tx_in2studyfinder_domain_model_academicdegree',
            1,
            1
        ),
        'department' => TcaUtility::getFullTcaForSingleSelect(
            $ll . 'department',
            'tx_in2studyfinder_domain_model_department'
        ),
        'faculty' => TcaUtility::getFullTcaForSingleSelect(
            $ll . 'faculty',
            'tx_in2studyfinder_domain_model_faculty'
        ),
        'types_of_study' => TcaUtility::getFullTcaForSelectSideBySide(
            $ll . 'typeOfStudy',
            'tx_in2studyfinder_domain_model_typeofstudy',
            'tx_in2studyfinder_studycourse_typeofstudy_mm',
            1,
            1
        ),
        'course_languages' => TcaUtility::getFullTcaForSelectSideBySide(
            $ll . 'courseLanguage',
            'tx_in2studyfinder_domain_model_courselanguage',
            'tx_in2studyfinder_studycourse_courselanguage_mm',
            1,
            1
        ),
        'admission_requirements' => TcaUtility::getFullTcaForSelectSideBySide(
            $ll . 'admissionRequirements',
            'tx_in2studyfinder_domain_model_admissionrequirement',
            'tx_in2studyfinder_studycourse_admissionrequirement_mm',
            1,
            1
        ),
        'starts_of_study' => TcaUtility::getFullTcaForSelectCheckBox(
            $ll . 'startOfStudy',
            'tx_in2studyfinder_domain_model_startofstudy',
            'tx_in2studyfinder_studycourse_startofstudy_mm',
            1,
            1
        ),
        'meta_pagetitle' => [
            'exclude' => 1,
            'label' => $ll . 'metaPageTitle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 100,
            ],
        ],
        'meta_keywords' => [
            'exclude' => 1,
            'label' => $ll . 'metaKeywords',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'meta_description' => [
            'exclude' => 1,
            'label' => $ll . 'metaDescription',
            'config' => [
                'type' => 'text',
                'cols' => '40',
                'rows' => '15',
                'eval' => 'trim',
                'max' => 750,
            ],
        ],
        'different_preset' => [
            'exclude' => 1,
            'label' => $ll . 'differentPreset',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'global_data_preset' => [
            'exclude' => 1,
            'l10n_mode' => 'exclude',
            'label' => $ll . 'globalDataPreset',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_in2studyfinder_domain_model_globaldata',
                'foreign_table_where' => 'AND sys_language_uid in (-1, 0)',
                'items' => [TcaUtility::getPleaseChooseOption('tx_in2studyfinder_domain_model_globaldata')],
                'minitems' => 1,
                'maxitems' => 1,
            ],
            'displayCond' => 'FIELD:different_preset:=:1',
        ],
    ],
];

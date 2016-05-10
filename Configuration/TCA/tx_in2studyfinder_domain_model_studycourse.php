<?php
use In2code\In2studyfinder\Utility\TcaGenerator;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$ll = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';
$table = 'tx_in2studyfinder_domain_model_studycourse';
$icon = ExtensionManagementUtility::extRelPath('in2studyfinder') . 'Resources/Public/Icons/' . $table . '.png';

return [
    'ctrl' => [
        'title' => $ll . $table,
        'label' => 'title',
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
            'admission_requirements, starts_of_study',
    ],
    'types' => [
        '0' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title,  ' .
                '--palette--;' . $ll . 'keyData;keyData,' .
                'teaser;;;richtext:rte_transform[mode=ts_links], description;;;richtext:rte_transform[mode=ts_links], '
                .
                'content_elements,  --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'
        ],
    ],
    'palettes' => [
        'keyData' => [
            'showitem' => 'standard_period_of_study, ects_credits, --linebreak--, ' .
                'tuition_fee, university_place, --linebreak--,' .
                'faculty, department, --linebreak--, ' .
                'academic_degree, starts_of_study, --linebreak--,' .
                'types_of_study, --linebreak--, admission_requirements, --linebreak--,' .
                'course_languages,'
        ],
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
        'standard_period_of_study' => [
            'exclude' => 1,
            'label' => $ll . $table . '.standard_period_of_study',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'ects_credits' => [
            'exclude' => 1,
            'label' => $ll . $table . '.ects_credits',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'tuition_fee' => [
            'exclude' => 1,
            'label' => $ll . $table . '.tuition_fee',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'double2'
            ]
        ],
        'teaser' => [
            'exclude' => 1,
            'label' => $ll . $table . '.teaser',
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
        'description' => [
            'exclude' => 1,
            'label' => $ll . $table . '.description',
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
            'label' => $ll . $table . '.university_place',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'content_elements' => [
            'exclude' => 1,
            'label' => $ll . $table . '.content_elements',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tt_content',
                'foreign_table' => 'tt_content',
                'MM' => 'tx_in2studyfinder_studycourse_ttcontent_mm',
                'maxitems' => 9999,
                'size' => 10,
                'wizards' => [
                    'suggest' => [
                        'type' => 'suggest',
                    ],
                ],
            ],
        ],
        'academic_degree' => TcaGenerator::getFullTcaForSingleSelect(
            $ll . $table . '.academic_degree',
            'tx_in2studyfinder_domain_model_academicdegree'
        ),
        'department' => TcaGenerator::getFullTcaForSingleSelect(
            $ll . $table . '.department',
            'tx_in2studyfinder_domain_model_department'
        ),
        'faculty' => TcaGenerator::getFullTcaForSingleSelect(
            $ll . $table . '.faculty',
            'tx_in2studyfinder_domain_model_faculty'
        ),
        'types_of_study' => TcaGenerator::getFullTcaForSelectSideBySide(
            $ll . $table . '.type_of_study',
            'tx_in2studyfinder_domain_model_typeofstudy',
            'tx_in2studyfinder_studycourse_typeofstudy_mm'
        ),
        'course_languages' => TcaGenerator::getFullTcaForSelectSideBySide(
            $ll . $table . '.course_language',
            'tx_in2studyfinder_domain_model_courselanguage',
            'tx_in2studyfinder_studycourse_courselanguage_mm'
        ),
        'admission_requirements' => TcaGenerator::getFullTcaForSelectSideBySide(
            $ll . $table . '.admission_requirement',
            'tx_in2studyfinder_domain_model_admissionrequirement',
            'tx_in2studyfinder_studycourse_admissionrequirement_mm'
        ),
        'starts_of_study' => TcaGenerator::getFullTcaForSelectCheckBox(
            $ll . $table . '.start_of_study',
            'tx_in2studyfinder_domain_model_startofstudy',
            'tx_in2studyfinder_studycourse_startofstudy_mm'
        ),
    ],
];

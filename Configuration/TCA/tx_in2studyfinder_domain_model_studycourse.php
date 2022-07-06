<?php

$ll = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';
$table = \In2code\In2studyfinder\Domain\Model\StudyCourse::TABLE;
$icon =
    TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName(
        'EXT:in2studyfinder/Resources/Public/Icons/' . $table . '.png'
    );

$tcaConfiguration = [
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
        'searchFields' => 'title,standard_period_of_study,ects_credits,tuition_fee,teaser,description,university_place,content_elements,academic_degree,department,faculty,types_of_study,course_languages,admission_requirements,starts_of_study,',
        'iconfile' => $icon,
    ],
    'types' => [
        '0' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, url_segment, --palette--;' . $ll . 'keyData;keyData,teaser, description, content_elements, --div--;' . $ll . 'metadata, --palette--;' . $ll . 'metadata;metadata, --div--;' . $ll . 'globalPreset, --palette--;' . $ll . 'globalPreset;globalPreset, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime,',
        ],
    ],
    'palettes' => [
        'keyData' => [
            'showitem' => 'academic_degree, --linebreak--, course_languages, --linebreak--, types_of_study, --linebreak--, admission_requirements, --linebreak--,  starts_of_study, --linebreak--, ects_credits, --linebreak--, tuition_fee, standard_period_of_study, --linebreak--, university_place, faculty, --linebreak--, department',
            'canNotCollapse' => 1,
        ],
        'metadata' => [
            'showitem' => 'meta_pagetitle, --linebreak--, meta_keywords, --linebreak--, meta_description',
            'canNotCollapse' => 1,
        ],
        'globalPreset' => [
            'showitem' => 'different_preset, --linebreak--, global_data_preset',
            'canNotCollapse' => 1,
        ],
    ],
    'columns' => [

        'sys_language_uid' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForSysLanguageUid(),
        'l10n_parent' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForL10nParent($table),
        'l10n_diffsource' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForL10nDiffsource(),
        't3ver_label' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForT3verLabel(),
        'hidden' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForHidden(),
        'starttime' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForStartTime(),
        'endtime' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForEndTime(),
        'title' => [
            'exclude' => true,
            'label' => $ll . 'title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
                'max' => 255,
            ],
        ],
        'standard_period_of_study' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'label' => $ll . 'standardPeriodOfStudy',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
            ],
        ],
        'ects_credits' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'label' => $ll . 'ectsCredits',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
            ],
        ],
        'tuition_fee' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'label' => $ll . 'tuitionFee',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'double2',
            ],
        ],
        'teaser' => [
            'exclude' => true,
            'label' => $ll . 'teaser',
            'config' => [
                'type' => 'text',
                'enableRichtext' => 1,
                'cols' => 40,
                'rows' => 15,
                'softref' => 'typolink_tag,email[subst],url',
                'eval' => 'trim,required',
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => $ll . 'description',
            'config' => [
                'type' => 'text',
                'enableRichtext' => 1,
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
                'softref' => 'typolink_tag,email[subst],url',
                'default' => '',
            ],
        ],
        'university_place' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'label' => $ll . 'universityPlace',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int',
            ],
        ],
        'content_elements' => [
            'exclude' => true,
            'label' => $ll . 'contentElements',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => \In2code\In2studyfinder\Domain\Model\TtContent::TABLE,
                'foreign_table' => \In2code\In2studyfinder\Domain\Model\TtContent::TABLE,
                'MM' => 'tx_in2studyfinder_studycourse_ttcontent_mm',
                'maxitems' => 9999,
                'size' => 10,
                'wizards' => [
                    'edit' => In2code\In2studyfinder\Utility\TcaUtility::getEditWizard(),
                    'suggest' => In2code\In2studyfinder\Utility\TcaUtility::getSuggestWizard(),
                ],
            ],
        ],
        'academic_degree' => [
            'exclude' => true,
            'label' => $ll . 'academicDegree',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tca.select.please_choose',
                        0,
                        'EXT:in2studyfinder/Resources/Public/Icons/' . \In2code\In2studyfinder\Domain\Model\AcademicDegree::TABLE . '.png'
                    ],
                ],
                'foreign_table' => \In2code\In2studyfinder\Domain\Model\AcademicDegree::TABLE,
                'foreign_table_where' => 'AND ' . \In2code\In2studyfinder\Domain\Model\AcademicDegree::TABLE . '.sys_language_uid in (-1, 0)',
                'default' => 0,
            ]
        ],
        'department' => [
            'exclude' => true,
            'label' => $ll . 'department',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tca.select.please_choose',
                        0,
                        'EXT:in2studyfinder/Resources/Public/Icons/' . \In2code\In2studyfinder\Domain\Model\Department::TABLE . '.png'
                    ],
                ],
                'foreign_table' => \In2code\In2studyfinder\Domain\Model\Department::TABLE,
                'foreign_table_where' => 'AND ' . \In2code\In2studyfinder\Domain\Model\Department::TABLE . '.sys_language_uid in (-1, 0)',
                'default' => 0,
            ]
        ],
        'faculty' => [
            'exclude' => true,
            'label' => $ll . 'faculty',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tca.select.please_choose',
                        0,
                        'EXT:in2studyfinder/Resources/Public/Icons/' . \In2code\In2studyfinder\Domain\Model\Faculty::TABLE . '.png'
                    ],
                ],
                'foreign_table' => \In2code\In2studyfinder\Domain\Model\Faculty::TABLE,
                'foreign_table_where' => 'AND ' . \In2code\In2studyfinder\Domain\Model\Faculty::TABLE . '.sys_language_uid in (-1, 0)',
                'default' => 0,
            ],
        ],
        'types_of_study' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForSelectSideBySide(
            $ll . 'typeOfStudy',
            \In2code\In2studyfinder\Domain\Model\TypeOfStudy::TABLE,
            'tx_in2studyfinder_studycourse_typeofstudy_mm',
            1,
            1
        ),
        'course_languages' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForSelectSideBySide(
            $ll . 'courseLanguage',
            \In2code\In2studyfinder\Domain\Model\CourseLanguage::TABLE,
            'tx_in2studyfinder_studycourse_courselanguage_mm',
            1,
            1
        ),
        'admission_requirements' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForSelectSideBySide(
            $ll . 'admissionRequirements',
            \In2code\In2studyfinder\Domain\Model\AdmissionRequirement::TABLE,
            'tx_in2studyfinder_studycourse_admissionrequirement_mm',
            1,
            1
        ),
        'starts_of_study' => In2code\In2studyfinder\Utility\TcaUtility::getFullTcaForSelectCheckBox(
            $ll . 'startOfStudy',
            \In2code\In2studyfinder\Domain\Model\StartOfStudy::TABLE,
            'tx_in2studyfinder_studycourse_startofstudy_mm',
            1,
            1
        ),
        'meta_pagetitle' => [
            'exclude' => true,
            'label' => $ll . 'metaPageTitle',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 100,
                'default' => ''
            ],
        ],
        'meta_keywords' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => $ll . 'metaKeywords',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'max' => 255,
                'default' => ''
            ],
        ],
        'meta_description' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => $ll . 'metaDescription',
            'config' => [
                'type' => 'text',
                'cols' => '40',
                'rows' => '15',
                'eval' => 'trim',
                'max' => 750,
                'default' => ''
            ],
        ],
        'url_segment' => [
            'label' => $ll . 'url_segment',
            'exclude' => true,
            'config' => [
                'type' => 'slug',
                'generatorOptions' => [
                    'fields' => ['title'],
                    'fieldSeparator' => '/',
                    'prefixParentPageSlug' => false,
                    'replacements' => [
                        '/' => '',
                    ],
                    'postModifiers' => [
                        \In2code\In2studyfinder\Slug\UrlSegmentPostModifier::class . '->extendWithGraduation'
                    ]
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
                'default' => ''
            ]
        ]
    ],
];

if (\In2code\In2studyfinder\Utility\ConfigurationUtility::isCategorisationEnabled()) {
    $tcaConfiguration['columns']['categories'] = [
        'config' => [
            'type' => 'category'
        ]
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        $table,
        'categories'
    );
}

if (In2code\In2studyfinder\Utility\ConfigurationUtility::isEnableGlobalData()) {
    $tcaConfiguration['columns']['different_preset'] = [
        'exclude' => true,
        'label' => $ll . 'differentPreset',
        'config' => [
            'type' => 'check',
            'onChange' => 'reload',
            'default' => 0,
        ],
    ];

    $tcaConfiguration['columns']['global_data_preset'] = [
        'exclude' => true,
        'label' => $ll . 'globalDataPreset',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'foreign_table' => \In2code\In2studyfinder\Domain\Model\GlobalData::TABLE,
            'foreign_table_where' => 'AND ' . \In2code\In2studyfinder\Domain\Model\GlobalData::TABLE . '.sys_language_uid in (-1, 0)',
            'items' => [
                In2code\In2studyfinder\Utility\TcaUtility::getPleaseChooseOption(
                    \In2code\In2studyfinder\Domain\Model\GlobalData::TABLE
                )
            ],
            'minitems' => 1,
            'maxitems' => 1,
        ],
        'displayCond' => 'FIELD:different_preset:=:1',
    ];
}

return $tcaConfiguration;

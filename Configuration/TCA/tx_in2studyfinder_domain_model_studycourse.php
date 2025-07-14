<?php

use In2code\In2studyfinder\Domain\Model\AcademicDegree;
use In2code\In2studyfinder\Domain\Model\AdmissionRequirement;
use In2code\In2studyfinder\Domain\Model\CourseLanguage;
use In2code\In2studyfinder\Domain\Model\Department;
use In2code\In2studyfinder\Domain\Model\Faculty;
use In2code\In2studyfinder\Domain\Model\GlobalData;
use In2code\In2studyfinder\Domain\Model\StartOfStudy;
use In2code\In2studyfinder\Domain\Model\StudyCourse;
use In2code\In2studyfinder\Domain\Model\TtContent;
use In2code\In2studyfinder\Domain\Model\TypeOfStudy;

$ll = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';

$tcaConfiguration = [
    'ctrl' => [
        'title' => $ll . 'studycourse',
        'label' => 'title',
        'label_alt' => 'academic_degree',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'sortby' => 'sorting',
        'versioningWS' => true,
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'languageField' => 'sys_language_uid',
        'translationSource' => 'l10n_source',
        'requestUpdate' => 'different_preset',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title,standard_period_of_study,ects_credits,tuition_fee,teaser,description,university_place,content_elements,academic_degree,department,faculty,types_of_study,course_languages,admission_requirements,starts_of_study,',
        'iconfile' => 'EXT:in2studyfinder/Resources/Public/Icons/' . StudyCourse::TABLE . '.png',
    ],
    'types' => [
        '0' => [
            'showitem' => 'title, url_segment, --palette--;' . $ll . 'keyData;keyData,teaser, description, content_elements,
            --div--;' . $ll . 'metadata, --palette--;' . $ll . 'metadata;metadata,
            --div--;' . $ll . 'globalPreset, --palette--;' . $ll . 'globalPreset;globalPreset,
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
        'standard_period_of_study' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'label' => $ll . 'standardPeriodOfStudy',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'number',
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
                'eval' => 'number',
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
                'eval' => 'number',
            ],
        ],
        'teaser' => [
            'exclude' => true,
            'label' => $ll . 'teaser',
            'l10n_mode' => 'prefixLangTitle',
            'config' => [
                'type' => 'text',
                'enableRichtext' => 1,
                'cols' => 40,
                'rows' => 15,
                'softref' => 'typolink_tag,email[subst],url',
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => $ll . 'description',
            'l10n_mode' => 'prefixLangTitle',
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
                'eval' => 'number',
            ],
        ],
        'content_elements' => [
            'exclude' => true,
            'label' => $ll . 'contentElements',
            'config' => [
                'type' => 'group',
                'allowed' => TtContent::TABLE,
                'MM' => 'tx_in2studyfinder_studycourse_ttcontent_mm',
                'maxitems' => 9999,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
                'size' => 10,
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
                        'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tca.select.please_choose',
                        'value' => 0,
                        'icon' =>  'EXT:in2studyfinder/Resources/Public/Icons/' . AcademicDegree::TABLE . '.png'
                    ],
                ],
                'foreign_table' => AcademicDegree::TABLE,
                'foreign_table_where' => 'AND ' . AcademicDegree::TABLE . '.sys_language_uid in (-1, 0)',
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
                        'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tca.select.please_choose',
                        'value' => 0,
                        'icon' =>  'EXT:in2studyfinder/Resources/Public/Icons/' . Department::TABLE . '.png'
                    ],
                ],
                'foreign_table' => Department::TABLE,
                'foreign_table_where' => 'AND ' . Department::TABLE . '.sys_language_uid in (-1, 0)',
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
                        'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tca.select.please_choose',
                        'value' => 0,
                        'icon' => 'EXT:in2studyfinder/Resources/Public/Icons/' . Faculty::TABLE . '.png'
                    ],
                ],
                'foreign_table' => Faculty::TABLE,
                'foreign_table_where' => 'AND ' . Faculty::TABLE . '.sys_language_uid in (-1, 0)',
                'default' => 0,
            ],
        ],
        'types_of_study' => [
            'label' => $ll . 'typeOfStudy',
            'exclude' => true,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => TypeOfStudy::TABLE,
                'foreign_table_where' => 'AND ' . TypeOfStudy::TABLE . '.sys_language_uid in (-1, 0)',
                'MM' => 'tx_in2studyfinder_studycourse_typeofstudy_mm',
                'minitems' => 1,
                'size' => 3,
                'autoSizeMax' => 10,
            ],
        ],
        'course_languages' => [
            'label' => $ll . 'courseLanguage',
            'exclude' => true,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => CourseLanguage::TABLE,
                'foreign_table_where' => 'AND ' . CourseLanguage::TABLE . '.sys_language_uid in (-1, 0)',
                'MM' => 'tx_in2studyfinder_studycourse_courselanguage_mm',
                'minitems' => 1,
                'size' => 3,
                'autoSizeMax' => 10,
            ],
        ],
        'admission_requirements' => [
            'label' => $ll . 'admissionRequirements',
            'exclude' => true,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => AdmissionRequirement::TABLE,
                'foreign_table_where' => 'AND ' . AdmissionRequirement::TABLE . '.sys_language_uid in (-1, 0)',
                'MM' => 'tx_in2studyfinder_studycourse_admissionrequirement_mm',
                'minitems' => 1,
                'size' => 3,
                'autoSizeMax' => 10,
            ],
        ],
        'starts_of_study' => [
            'label' => $ll . 'startOfStudy',
            'exclude' => true,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectCheckBox',
                'foreign_table' => StartOfStudy::TABLE,
                'foreign_table_where' => 'AND ' . StartOfStudy::TABLE . '.sys_language_uid in (-1, 0)',
                'MM' => 'tx_in2studyfinder_studycourse_startofstudy_mm',
                'minitems' => 1,
                'size' => 3,
                'autoSizeMax' => 10,
            ],
        ],
        'meta_pagetitle' => [
            'exclude' => true,
            'label' => $ll . 'metaPageTitle',
            'l10n_mode' => 'prefixLangTitle',
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
            'l10n_mode' => 'prefixLangTitle',
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
            'l10n_mode' => 'prefixLangTitle',
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

$tcaConfiguration['types']['0']['showitem'] .= ',--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories, categories,';
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
            'foreign_table' => GlobalData::TABLE,
            'foreign_table_where' => 'AND ' . GlobalData::TABLE . '.sys_language_uid in (-1, 0)',
            'items' => [
                'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tca.select.please_choose',
                'value' => 0,
                'icon' => 'EXT:in2studyfinder/Resources/Public/Icons/' . GlobalData::TABLE . '.png'
            ],
            'minitems' => 1,
            'maxitems' => 1,
        ],
        'displayCond' => 'FIELD:different_preset:=:1',
    ];
}

return $tcaConfiguration;

<?php
return array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'title,standard_period_of_study,ects_credits,tuition_fee,teaser,description,university_place,content_elements,academic_degree,department,faculty,type_of_study,course_language,admission_requirements,start_of_study,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('in2studyfinder') . 'Resources/Public/Icons/tx_in2studyfinder_domain_model_studycourse.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, standard_period_of_study, ects_credits, tuition_fee, teaser, description, university_place, content_elements, academic_degree, department, faculty, type_of_study, course_language, admission_requirements, start_of_study',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, standard_period_of_study, ects_credits, tuition_fee, teaser;;;richtext:rte_transform[mode=ts_links], description;;;richtext:rte_transform[mode=ts_links], university_place, content_elements, academic_degree, department, faculty, type_of_study, course_language, admission_requirements, start_of_study, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
	
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_in2studyfinder_domain_model_studycourse',
				'foreign_table_where' => 'AND tx_in2studyfinder_domain_model_studycourse.pid=###CURRENT_PID### AND tx_in2studyfinder_domain_model_studycourse.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),

		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
	
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),

		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.title',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'standard_period_of_study' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.standard_period_of_study',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			)
		),
		'ects_credits' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.ects_credits',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			)
		),
		'tuition_fee' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.tuition_fee',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'double2'
			)
		),
		'teaser' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.teaser',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim',
				'wizards' => array(
					'RTE' => array(
						'icon' => 'wizard_rte2.gif',
						'notNewRecords'=> 1,
						'RTEonly' => 1,
						'module' => array(
							'name' => 'wizard_rich_text_editor',
							'urlParameters' => array(
								'mode' => 'wizard',
								'act' => 'wizard_rte.php'
							)
						),
						'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
						'type' => 'script'
					)
				)
			),
		),
		'description' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim',
				'wizards' => array(
					'RTE' => array(
						'icon' => 'wizard_rte2.gif',
						'notNewRecords'=> 1,
						'RTEonly' => 1,
						'module' => array(
							'name' => 'wizard_rich_text_editor',
							'urlParameters' => array(
								'mode' => 'wizard',
								'act' => 'wizard_rte.php'
							)
						),
						'title' => 'LLL:EXT:cms/locallang_ttc.xlf:bodytext.W.RTE',
						'type' => 'script'
					)
				)
			),
		),
		'university_place' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.university_place',
			'config' => array(
				'type' => 'input',
				'size' => 4,
				'eval' => 'int'
			)
		),
		'content_elements' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.content_elements',
			'config' => array(
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
			),
		),
		'academic_degree' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.academic_degree',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_in2studyfinder_domain_model_academicdegree',
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
		'department' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.department',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_in2studyfinder_domain_model_department',
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
		'faculty' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.faculty',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_in2studyfinder_domain_model_faculty',
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
		'type_of_study' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.type_of_study',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_in2studyfinder_domain_model_typeofstudy',
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
		'course_language' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.course_language',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_in2studyfinder_domain_model_courselanguage',
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
		'admission_requirements' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.admission_requirements',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_in2studyfinder_domain_model_admissionrequirements',
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
		'start_of_study' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.start_of_study',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_in2studyfinder_domain_model_startofstudy',
				'minitems' => 0,
				'maxitems' => 1,
			),
		),
		
	),
);

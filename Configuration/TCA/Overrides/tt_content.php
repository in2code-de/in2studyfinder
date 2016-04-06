<?php

if (!isset($GLOBALS['TCA']['tt_content']['ctrl']['type'])) {
	if (file_exists($GLOBALS['TCA']['tt_content']['ctrl']['dynamicConfigFile'])) {
		require_once($GLOBALS['TCA']['tt_content']['ctrl']['dynamicConfigFile']);
	}
	// no type field defined, so we define it here. This will only happen the first time the extension is installed!!
	$GLOBALS['TCA']['tt_content']['ctrl']['type'] = 'tx_extbase_type';
	$tempColumnstx_in2studyfinder_tt_content = array();
	$tempColumnstx_in2studyfinder_tt_content[$GLOBALS['TCA']['tt_content']['ctrl']['type']] = array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder.tx_extbase_type',
		'config' => array(
			'type' => 'select',
			'renderType' => 'selectSingle',
			'items' => array(
				array('TtContent','Tx_In2studyfinder_TtContent')
			),
			'default' => 'Tx_In2studyfinder_TtContent',
			'size' => 1,
			'maxitems' => 1,
		)
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $tempColumnstx_in2studyfinder_tt_content, 1);
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'tt_content',
	$GLOBALS['TCA']['tt_content']['ctrl']['type'],
	'',
	'after:' . $GLOBALS['TCA']['tt_content']['ctrl']['label']
);

$tmp_in2studyfinder_columns = array(

	'studycourse' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_ttcontent.studycourse',
		'config' => array(
			'type' => 'input',
			'size' => 4,
			'eval' => 'int'
		)
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content',$tmp_in2studyfinder_columns);

/* inherit and extend the show items from the parent class */

if(isset($GLOBALS['TCA']['tt_content']['types']['1']['showitem'])) {
	$GLOBALS['TCA']['tt_content']['types']['Tx_In2studyfinder_TtContent']['showitem'] = $GLOBALS['TCA']['tt_content']['types']['1']['showitem'];
} elseif(is_array($GLOBALS['TCA']['tt_content']['types'])) {
	// use first entry in types array
	$tt_content_type_definition = reset($GLOBALS['TCA']['tt_content']['types']);
	$GLOBALS['TCA']['tt_content']['types']['Tx_In2studyfinder_TtContent']['showitem'] = $tt_content_type_definition['showitem'];
} else {
	$GLOBALS['TCA']['tt_content']['types']['Tx_In2studyfinder_TtContent']['showitem'] = '';
}
$GLOBALS['TCA']['tt_content']['types']['Tx_In2studyfinder_TtContent']['showitem'] .= ',--div--;LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_ttcontent,';
$GLOBALS['TCA']['tt_content']['types']['Tx_In2studyfinder_TtContent']['showitem'] .= 'studycourse';

$GLOBALS['TCA']['tt_content']['columns'][$GLOBALS['TCA']['tt_content']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tt_content.tx_extbase_type.Tx_In2studyfinder_TtContent','Tx_In2studyfinder_TtContent');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'',
	'EXT:/Resources/Private/Language/locallang_csh_.xlf'
);
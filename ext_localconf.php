<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'In2code.' . $_EXTKEY,
	'Studycourse',
	array(
		'StudyCourse' => 'list, show',
		
	),
	// non-cacheable actions
	array(
		'StudyCourse' => 'list, show',
		
	)
);

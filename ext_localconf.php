<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$extKey = 'in2studyfinder';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'In2code.' . $extKey,
	'Studycourse',
	array(
		'StudyCourse' => 'list, detail, filter',
	),
	// non-cacheable actions
	array(
		'StudyCourse' => 'list, detail, filter',
	)
);

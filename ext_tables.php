<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'In2code.' . $_EXTKEY,
	'Studycourse',
	'StudyCourse'
);

if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'In2code.' . $_EXTKEY,
		'web',	 // Make module a submodule of 'web'
		'studyfinder',	// Submodule key
		'',						// Position
		array(
			'StudyCourse' => 'list, show',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_studyfinder.xlf',
		)
	);

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'in2studyfinder');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_in2studyfinder_domain_model_studycourse', 'EXT:in2studyfinder/Resources/Private/Language/locallang_csh_tx_in2studyfinder_domain_model_studycourse.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_in2studyfinder_domain_model_studycourse');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_in2studyfinder_domain_model_academicdegree', 'EXT:in2studyfinder/Resources/Private/Language/locallang_csh_tx_in2studyfinder_domain_model_academicdegree.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_in2studyfinder_domain_model_academicdegree');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_in2studyfinder_domain_model_department', 'EXT:in2studyfinder/Resources/Private/Language/locallang_csh_tx_in2studyfinder_domain_model_department.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_in2studyfinder_domain_model_department');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_in2studyfinder_domain_model_faculty', 'EXT:in2studyfinder/Resources/Private/Language/locallang_csh_tx_in2studyfinder_domain_model_faculty.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_in2studyfinder_domain_model_faculty');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_in2studyfinder_domain_model_typeofstudy', 'EXT:in2studyfinder/Resources/Private/Language/locallang_csh_tx_in2studyfinder_domain_model_typeofstudy.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_in2studyfinder_domain_model_typeofstudy');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_in2studyfinder_domain_model_courselanguage', 'EXT:in2studyfinder/Resources/Private/Language/locallang_csh_tx_in2studyfinder_domain_model_courselanguage.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_in2studyfinder_domain_model_courselanguage');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_in2studyfinder_domain_model_admissionrequirements', 'EXT:in2studyfinder/Resources/Private/Language/locallang_csh_tx_in2studyfinder_domain_model_admissionrequirements.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_in2studyfinder_domain_model_admissionrequirements');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_in2studyfinder_domain_model_startofstudy', 'EXT:in2studyfinder/Resources/Private/Language/locallang_csh_tx_in2studyfinder_domain_model_startofstudy.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_in2studyfinder_domain_model_startofstudy');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    $_EXTKEY,
    'tx_in2studyfinder_domain_model_studycourse'
);

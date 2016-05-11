<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$extKey = 'in2studyfinder';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'In2code.' . $extKey,
    'Studycourse',
    'StudyCourse'
);

if (TYPO3_MODE === 'BE') {

    /**
     * Registers a Backend Module
     */
//    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
//        'In2code.' . $extKey,
//        'web',
//        'studyfinder',
//        '',
//        array(
//            'BackendModule' => 'list, generateDummyData',
//        ),
//        array(
//            'access' => 'user,group',
//            'icon' => 'EXT:' . $extKey . '/ext_icon.png',
//            'labels' => 'LLL:EXT    :' . $extKey . '/Resources/Private/Language/locallang_studyfinder.xlf',
//        )
//    );

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $extKey, 'Configuration/TypoScript/Main', 'In2studyfinder Basic Template'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $extKey, 'Configuration/TypoScript/Css', 'In2studyfinder Demo CSS Template'
);

$tables = [
    'tx_in2studyfinder_domain_model_studycourse',
    'tx_in2studyfinder_domain_model_academicdegree',
    'tx_in2studyfinder_domain_model_department',
    'tx_in2studyfinder_domain_model_faculty',
    'tx_in2studyfinder_domain_model_typeofstudy',
    'tx_in2studyfinder_domain_model_courselanguage',
    'tx_in2studyfinder_domain_model_admissionrequirement',
    'tx_in2studyfinder_domain_model_startofstudy',
    'tx_in2studyfinder_domain_model_graduation'
];

foreach ($tables as $table) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
        $table,
        'EXT:in2studyfinder/Resources/Private/Language/locallang_csh_' . $table . '.xlf'
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
        $table
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    $extKey,
    'tx_in2studyfinder_domain_model_studycourse'
);

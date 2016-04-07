<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$extensionKey = 'in2studyfinder';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'In2code.' . $extensionKey,
    'Studycourse',
    'StudyCourse'
);

if (TYPO3_MODE === 'BE') {

    /**
     * Registers a Backend Module
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'In2code.' . $extensionKey,
        'web',     // Make module a submodule of 'web'
        'studyfinder',    // Submodule key
        '',                        // Position
        array(
            'StudyCourse' => 'list, show',
        ),
        array(
            'access' => 'user,group',
            'icon' => 'EXT:' . $extensionKey . '/ext_icon.gif',
            'labels' => 'LLL:EXT:' . $extensionKey . '/Resources/Private/Language/locallang_studyfinder.xlf',
        )
    );

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $extensionKey, 'Configuration/TypoScript', 'in2studyfinder'
);

$tables = [
    'tx_in2studyfinder_domain_model_studycourse',
    'tx_in2studyfinder_domain_model_academicdegree',
    'tx_in2studyfinder_domain_model_department',
    'tx_in2studyfinder_domain_model_faculty',
    'tx_in2studyfinder_domain_model_typeofstudy',
    'tx_in2studyfinder_domain_model_courselanguage',
    'tx_in2studyfinder_domain_model_admissionrequirements',
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
    $extensionKey,
    'tx_in2studyfinder_domain_model_studycourse'
);

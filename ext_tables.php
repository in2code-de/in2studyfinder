<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$extKey = 'in2studyfinder';

/**
 * Include Plugins
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'In2code.' . $extKey,
    'Pi1',
    'Studiengangsfinder Listenansicht'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'In2code.' . $extKey,
    'Pi2',
    'Studiengangsfinder Detailansicht'
);

if (TYPO3_MODE === 'BE') {

    if (\In2code\In2studyfinder\Utility\VersionUtility::isTypo3MajorVersionAbove(6)) {

        /**
         * Register Icons
         */
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'in2studyfinder-plugin-icon',
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:in2studyfinder/ext_icon.png']
        );

        /**
         * Add to ContentElementWizard
         */
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:in2studyfinder/Configuration/TSConfig/ContentElementWizard.typoscript">'
        );
    } else {

        /**
         * Add to ContentElementWizard
         */
        $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['In2code\In2studyfinder\Utility\Hook\WizIcon'] =
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extKey) . 'Classes/Utility/Hook/WizIcon.php';
    }
}

/**
 * Include Flexform
 */
$pluginSignature = str_replace('_', '', $extKey) . '_pi1';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:' . $extKey . '/Configuration/FlexForms/FlexformStudyfinderList.xml'
);

$pluginSignature = str_replace('_', '', $extKey) . '_pi2';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:' . $extKey . '/Configuration/FlexForms/FlexformStudyfinderDetail.xml'
);

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

if (\In2code\In2studyfinder\Utility\ConfigurationUtility::isCategorisationEnabled()) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
        $extKey,
        'tx_in2studyfinder_domain_model_studycourse'
    );
}

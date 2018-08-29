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

    /**
     * Include Backend Module
     */
    if (TYPO3_MODE === 'BE' && \In2code\In2studyfinder\Utility\ConfigurationUtility::isBackendModuleEnabled()) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'In2code.in2studyfinder',
            'web',
            'm1',
            '',
            [
                'Backend' => 'list, export'
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:in2studyfinder/ext_icon.svg',
                'labels' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:wizardItemTitle',
                'navigationComponentId' => '',
            ]
        );
    }

    /**
     * Register Icons
     */
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Imaging\IconRegistry::class
    );
    $iconRegistry->registerIcon(
        'in2studyfinder-plugin-icon',
        \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
        ['source' => 'EXT:in2studyfinder/ext_icon.svg']
    );

    /**
     * Add to ContentElementWizard
     */
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:in2studyfinder/Configuration/TSConfig/ContentElementWizard.typoscript">'
    );

    /**
     * Register default export types
     */
    $GLOBALS['TYPO3_CONF_VARS']['EXT']['in2studyfinder']['exportTypes']['CSV'] =
        \In2code\In2studyfinder\Export\ExportTypes\CsvExport::class;

}

/**
 * Include Flexform
 */
$flexformConfiguration = [
    str_replace('_', '', $extKey) . '_pi1' =>
        'FILE:EXT:' . $extKey . '/Configuration/FlexForms/FlexformStudyfinderList.xml',
    str_replace('_', '', $extKey) . '_pi2' =>
        'FILE:EXT:' . $extKey . '/Configuration/FlexForms/FlexformStudyfinderDetail.xml'
];

foreach ($flexformConfiguration as $pluginSignature => $flexformPath) {
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        $flexformPath
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $extKey,
    'Configuration/TypoScript/Main',
    'In2studyfinder Basic Template'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $extKey,
    'Configuration/TypoScript/Css',
    'In2studyfinder Demo CSS Template'
);

$tables = [
    \In2code\In2studyfinder\Domain\Model\StudyCourse::TABLE,
    \In2code\In2studyfinder\Domain\Model\AcademicDegree::TABLE,
    \In2code\In2studyfinder\Domain\Model\Department::TABLE,
    \In2code\In2studyfinder\Domain\Model\Faculty::TABLE,
    \In2code\In2studyfinder\Domain\Model\TypeOfStudy::TABLE,
    \In2code\In2studyfinder\Domain\Model\CourseLanguage::TABLE,
    \In2code\In2studyfinder\Domain\Model\AdmissionRequirement::TABLE,
    \In2code\In2studyfinder\Domain\Model\StartOfStudy::TABLE,
    \In2code\In2studyfinder\Domain\Model\Graduation::TABLE,
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
        \In2code\In2studyfinder\Domain\Model\StudyCourse::TABLE
    );
}

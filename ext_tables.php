<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$ll = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';
$controller = \In2code\In2studyfinder\Controller\BackendController::class;
$extensionName = 'In2studyfinder';

/**
 * Include Plugins
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'In2studyfinder',
    'Pi1',
    $ll . 'plugin.pi1'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'In2studyfinder',
    'FastSearch',
    $ll . 'plugin.fastSearch'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'In2studyfinder',
    'Pi2',
    $ll . 'plugin.pi2'
);

if (TYPO3_MODE === 'BE') {

    /**
     * Include Backend Module
     */
    if (TYPO3_MODE === 'BE' && \In2code\In2studyfinder\Utility\ConfigurationUtility::isBackendModuleEnabled()) {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            $extensionName,
            'web',
            'm1',
            '',
            [
                $controller => 'list, export'
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:in2studyfinder/Resources/Public/Icons/Extension.svg',
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
        ['source' => 'EXT:in2studyfinder/Resources/Public/Icons/Extension.svg']
    );
    $iconRegistry->registerIcon(
        'in2studyfinder-chatbot-icon',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:in2studyfinder/Resources/Public/Icons/chatbot.svg']
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
    str_replace('_', '', 'in2studyfinder') . '_pi1' =>
        'FILE:EXT:in2studyfinder' . '/Configuration/FlexForms/FlexformStudyfinderList.xml',
    str_replace('_', '', 'in2studyfinder') . '_pi2' =>
        'FILE:EXT:in2studyfinder' . '/Configuration/FlexForms/FlexformStudyfinderDetail.xml',
    str_replace('_', '', 'in2studyfinder') . '_fastsearch' =>
        'FILE:EXT:in2studyfinder/Configuration/FlexForms/FlexformStudyfinderFastSearch.xml'
];

foreach ($flexformConfiguration as $pluginSignature => $flexformPath) {
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        $flexformPath
    );
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'in2studyfinder',
    'Configuration/TypoScript/Main',
    'In2studyfinder Basic Template'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'in2studyfinder',
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

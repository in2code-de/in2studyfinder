<?php

defined('TYPO3') or die();

/**
 * filter plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'In2studyfinder',
    'Pi1',
    'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:plugin.pi1'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['in2studyfinder_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'in2studyfinder_pi1',
    'FILE:EXT:in2studyfinder/Configuration/FlexForms/FlexformStudyfinderList.xml'
);

/**
 * detail plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'In2studyfinder',
    'Pi2',
    'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:plugin.pi2'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['in2studyfinder_pi2'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'in2studyfinder_pi2',
    'FILE:EXT:in2studyfinder/Configuration/FlexForms/FlexformStudyfinderDetail.xml'
);

/**
 * fast search plugin
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'In2studyfinder',
    'FastSearch',
    'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:plugin.fastSearch'
);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['in2studyfinder_fastsearch'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'in2studyfinder_fastsearch',
    'FILE:EXT:in2studyfinder/Configuration/FlexForms/FlexformStudyfinderFastSearch.xml'
);

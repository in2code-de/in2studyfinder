<?php

defined('TYPO3') or die();

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

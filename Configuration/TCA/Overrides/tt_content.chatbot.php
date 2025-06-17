<?php

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:in2studyfinder/Configuration/FlexForms/FlexformStudyfinderChatbot.xml',
    'in2studyfinder_chatbot'
);

$GLOBALS['TCA']['tt_content']['types']['in2studyfinder_chatbot']['showitem'] = '
--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general, --palette--;;general, --palette--;;headers,pi_flexform,
--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance, --palette--;;frames, --palette--;;appearanceLinks,
--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language, --palette--;;language,
--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access, --palette--;;hidden, --palette--;;access,
--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories, categories,
--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes, rowDescription,
--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended';

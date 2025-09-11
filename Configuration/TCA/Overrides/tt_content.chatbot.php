<?php

$ll = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';
$pluginName = 'in2studyfinder_chatbot';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'in2studyfinder',
    'Chatbot',
    $ll . 'chatbot.plugin.title',
    'in2studyfinder-chatbot-icon',
        'Studyfinder',
        $ll . 'chatbot.plugin.description'
);


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    'pi_flexform',
    $pluginName,
    'after:palette:headers'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:in2studyfinder/Configuration/FlexForm/Plugin/Chatbot.xml',
    $pluginName
);

$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$pluginName] = 'in2studyfinder-chatbot-icon';

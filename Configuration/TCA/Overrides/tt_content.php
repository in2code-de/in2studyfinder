<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

$extensionKey = 'In2studyfinder';
$languagePrefix = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';
$flexFormFolder = 'FILE:EXT:in2studyfinder/Configuration/FlexForm/Plugin/';

$plugins = [
    'filter' => [
        'name' => 'Filter',
        'title' => $languagePrefix . 'plugin.pi1',
        'flexForm' => $flexFormFolder . 'Filter.xml',
    ],
    'detail' => [
        'name' => 'Detail',
        'title' => $languagePrefix . 'plugin.pi2',
        'flexForm' => $flexFormFolder . 'Detail.xml',
    ],
    'fastsearch' => [
        'name' => 'Fastsearch',
        'title' => $languagePrefix . 'plugin.fastSearch',
        'flexForm' => $flexFormFolder . 'FastSearch.xml',
    ],
];

foreach ($plugins as $plugin) {
    $pluginSignature = ExtensionUtility::registerPlugin(
        $extensionKey,
        $plugin['name'],
        $plugin['title'],
        'in2studyfinder-plugin-icon',
        'Studyfinder',
        'Meine tolle beschreibung!'
    );

    ExtensionManagementUtility::addToAllTCAtypes(
        'tt_content',
        '--div--;Configuration,pi_flexform,recursive,pages',
        $pluginSignature,
        'after:subheader',
    );

    ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        $plugin['flexForm'],
        $pluginSignature,
    );
}

<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$extKey = 'in2studyfinder';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'In2code.' . $extKey,
    'Pi1',
    array(
        'StudyCourse' => 'list, filter',
    ),
    // non-cacheable actions
    array(
        'StudyCourse' => 'list, filter',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'In2code.' . $extKey,
    'Pi2',
    array(
        'StudyCourse' => 'detail',
    ),
    // non-cacheable actions
    array(
        'StudyCourse' => 'detail',
    )
);

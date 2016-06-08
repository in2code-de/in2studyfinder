<?php
namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class TcaGenerator
 *
 * @package In2code\In2studyfinder\Utility
 */
class TcaGenerator
{

    /**
     * Gets full Tca Array for Sys Language Uid
     * @return array
     */
    public static function getFullTcaForSysLanguageUid()
    {
        return [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0]
                ],
            ],
        ];
    }

    /**
     * @param $table
     * @return array
     */
    public static function getFullTcaForL10nParent($table)
    {
        return [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => $table,
                'foreign_table_where' => 'AND ' . $table . '.pid=###CURRENT_PID### AND ' . $table
                    . '.sys_language_uid IN (-1,0)',
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getFullTcaForL10nDiffsource()
    {
        return [
            'config' => [
                'type' => 'passthrough',
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getFullTcaForT3verLabel()
    {
        return [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ]
        ];
    }

    /**
     * @return array
     */
    public static function getFullTcaForHidden()
    {
        return [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getFullTcaForStartTime()
    {
        return [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ),
            ),
        ];
    }

    /**
     * @return array
     */
    public static function getFullTcaForEndTime()
    {
        return [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => array(
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => array(
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ),
            ),
        ];
    }

    /**
     * @param string $table
     * @return array
     */
    public static function getPleaseChooseOption($table = '')
    {
        $icon = '';

        if ($table !== '') {
            $icon = ExtensionManagementUtility::extRelPath('in2studyfinder') .
                'Resources/Public/Icons/' . $table . '.png';
        }

        return [
            'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tca.select.please_choose',
            '',
            $icon,
        ];
    }

    /**
     * @param string $label
     * @param string $table
     * @param int $minItems
     * @param int $exclude
     * @return array
     */
    public static function getFullTcaForSingleSelect($label, $table, $exclude = 1, $minItems = 0) {
        return [
            'exclude' => $exclude,
            'label' => $label,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => $table,
                'foreign_table_where' => 'AND sys_language_uid in (-1, 0)',
                'items' => [self::getPleaseChooseOption($table)],
                'minitems' => $minItems,
            ],
        ];
    }

    public static function getFullTcaForSelectCheckBox($label, $table, $mmTable, $exclude = 1, $minItems = 0, $maxItems = 5) {
        return [
            'exclude' => $exclude,
            'label' => $label,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectCheckBox',
                'foreign_table' => $table,
                'MM' => $mmTable,
                'foreign_table_where' => 'AND sys_language_uid in (-1, 0)',
                'minitems' => $minItems,
                'maxitems' => $maxItems,
            ],
        ];
    }

    public static function getFullTcaForSelectSideBySide($label, $table, $mmTable, $exclude = 1, $minItems = 0, $maxItems = 9999) {
        return [
            'exclude' => $exclude,
            'label' => $label,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => $table,
                'MM' => $mmTable,
                'foreign_table_where' => 'AND sys_language_uid in (-1, 0)',
                'minitems' => $minItems,
                'maxitems' => $maxItems,
                'wizards' => [
                    'suggest' => [
                        'type' => 'suggest',
                    ],
                ],
            ],
        ];
    }
}

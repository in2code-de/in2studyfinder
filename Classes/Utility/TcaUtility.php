<?php
namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Class TcaUtility
 *
 * @package In2code\In2studyfinderExtend\Utility
 */
class TcaUtility
{
    /**
     * Gets full Tca Array for Sys Language Uid
     *
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
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0],
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
            ],
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
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
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
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
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
    public static function getFullTcaForSingleSelect(
        $label,
        $table,
        $exclude = 1,
        $minItems = 0
    ) {
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

    public static function getFullTcaForSelectCheckBox(
        $label,
        $table,
        $mmTable,
        $exclude = 1,
        $minItems = 0,
        $maxItems = 5
    ) {
        /**
         * Compatibility for Typo3 6.2 LTS
         */
        if (ExtensionUtility::isTypo3MajorVersionBelow(7)) {
            return self::getFullTcaForSelectSideBySide($label, $table, $mmTable);
        } else {
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
    }

    public static function getFullTcaForSelectSideBySide(
        $label,
        $table,
        $mmTable,
        $exclude = 1,
        $minItems = 0,
        $maxItems = 9999
    ) {
        if (ExtensionUtility::isTypo3MajorVersionBelow(7)) {
            return [
                'exclude' => $exclude,
                'label' => $label,
                'config' => [
                    'type' => 'select',
                    'foreign_table' => $table,
                    'foreign_table_where' => 'AND sys_language_uid in (-1, 0)',
                    'size' => 5,
                    'autoSizeMax' => 30,
                    'maxitems' => 9999,
                    'multiple' => 0,
                    'wizards' => [
                        'suggest' => [
                            'type' => 'suggest',
                        ],
                    ],
                ],
            ];
        } else {
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

    /**
     * Gets the Suggest Wizard
     *
     * @return array
     */
    public static function getSuggestWizard()
    {
        return [
            'suggest' => [
                'type' => 'suggest',
            ],
        ];
    }

    /**
     * @param $table
     * @param $extbaseTypeValue
     * @param string $insertAfter
     */
    public static function setExtbaseType(
        $table,
        $extbaseTypeValue,
        $insertAfter = 'title'
    ) {
        if (!isset($GLOBALS['TCA'][$table]['ctrl']['type'])) {
            $GLOBALS['TCA'][$table]['ctrl']['type'] = 'tx_extbase_type';
            $extbaseType = [];
            $extbaseType['tx_extbase_type'] = [
                'exclude' => 1,
                'label' => 'LLL:EXT:in2studyfinder_extend/Resources/Private/Language/locallang_db.xlf:tx_in2studyfinder_domain_model_studycourse.tx_extbase_type.Tx_In2studyfinderExtend_StudyCourse',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        ['Erweiterter Studiengang [TODO] Übersetzen', $extbaseTypeValue],
                        ['Standard Studiengang [TODO] Übersetzen', ''],
                    ],
                    'default' => $extbaseTypeValue,
                    'size' => 1,
                    'maxitems' => 1,
                ],
            ];

            ExtensionManagementUtility::addTCAcolumns(
                $table,
                $extbaseType,
                1
            );
        }

        ExtensionManagementUtility::addToAllTCAtypes(
            $table,
            $GLOBALS['TCA'][$table]['ctrl']['type'],
            '',
            'after:' . $insertAfter
        );
    }

    /**
     * @param $table
     * @param $extbaseType
     * @param $field
     * @param $insertAfter
     */
    public static function addFieldToShowItem(
        $table,
        $extbaseType,
        $field,
        $insertAfter = 'last'
    ) {
        $newShowItem = $GLOBALS['TCA'][$table]['types']['0']['showitem'];

        $fieldArray = explode(',', $newShowItem);

        array_walk($fieldArray, [self::class, 'trimValue']);

        if (in_array($insertAfter, $fieldArray)) {
            $arrayKey = array_search($insertAfter, $fieldArray) + 1;
            array_splice($fieldArray, $arrayKey, 0, [$field]);
        } else {
            array_push($fieldArray, $field);
            $fieldArray = array_filter($fieldArray);
        }

        $GLOBALS['TCA'][$table]['types'][$extbaseType]['showitem'] = implode(',', $fieldArray);
    }

    /**
     * @param $table
     * @param $palette
     * @param $field
     * @param string $insertAfter
     * @param bool|false $addLineBreakAfter
     * @param bool|false $addLineBreakBefore
     */
    public static function addFieldToPalettes(
        $table,
        $palette,
        $field,
        $insertAfter = 'last',
        $addLineBreakBefore = false,
        $addLineBreakAfter = false
    ) {
        $newShowItem = $GLOBALS['TCA'][$table]['palettes'][$palette]['showitem'];
        $fieldArray = explode(',', $newShowItem);

        if ($addLineBreakBefore) {
            $insertArray[0] = '--linebreak--';
        }

        $insertArray[1] = $field;

        if ($addLineBreakAfter) {
            $insertArray[2] = '--linebreak--';
        }

        array_walk($fieldArray, [self::class, 'trimValue']);

        if (in_array($insertAfter, $fieldArray)) {
            $arrayKey = array_search($insertAfter, $fieldArray) + 1;
            array_splice($fieldArray, $arrayKey, 0, $insertArray);
        } else {
            foreach ($insertArray as $value) {
                array_push($fieldArray, $value);
            }

            $fieldArray = array_filter($fieldArray);
        }

        $GLOBALS['TCA'][$table]['palettes'][$palette]['showitem'] = implode(',', $fieldArray);
    }

    /**
     * @param $table
     * @param $localLangPath
     * @param $localLangId
     * @param $fields
     * @param $insertAfter
     */
    public static function addFieldsInNewDiv(
        $table,
        $localLangPath,
        $localLangId,
        $fields,
        $insertAfter
    ) {
        $fieldString = implode(',', $fields);

        $tab = ', --div--;' . $localLangPath . ':' . $localLangId . ',' . $fieldString . ',';

        ExtensionManagementUtility::addToAllTCAtypes($table, $tab, '', 'after:' . $insertAfter);
    }

    /**
     * @param $table
     * @param $palette
     * @param $field
     */
    public static function removeFieldFromPaletteShowItem(
        $table,
        $palette,
        $field
    ) {
        $showItemArray = explode(',', $GLOBALS['TCA'][$table]['palettes'][$palette]['showitem']);

        if (in_array($field, $showItemArray)) {
            unset(
                $showItemArray[array_search($field, $showItemArray)]
            );
        }

        $GLOBALS['TCA'][$table]['palettes'][$palette]['showitem'] = implode(',', $showItemArray);
    }

    /**
     * @param $table
     * @param $type
     * @param $field
     */
    public static function removeFieldFromShowItem(
        $table,
        $type,
        $field
    ) {
        $showItemArray = explode(',', $GLOBALS['TCA'][$table]['types'][$type]['showitem']);

        if (in_array($field, $showItemArray)) {
            unset(
                $showItemArray[array_search($field, $showItemArray)]
            );
        }

        $GLOBALS['TCA'][$table]['types'][$type]['showitem'] = implode(',', $showItemArray);
    }

    /**
     * @param string $value
     */
    public static function trimValue(&$value)
    {
        $value = trim($value);
    }
}

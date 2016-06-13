<?php
namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class TcaUtility
 *
 * @package In2code\In2studyfinderExtend\Utility
 */
class TcaUtility
{
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
                        ['Standard Studiengang [TODO] Übersetzen', '']
                    ],
                    'default' => $extbaseTypeValue,
                    'size' => 1,
                    'maxitems' => 1,
                ]
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
    public static function removeFieldFromShowItem($table, $type, $field)
    {
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

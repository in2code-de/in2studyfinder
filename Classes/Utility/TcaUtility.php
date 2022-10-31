<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Utility;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class TcaUtility extends AbstractUtility
{
    /**
     * @return array
     * @deprecated will be removed in v9, define the sys_language_uid tca manually
     */
    public static function getFullTcaForSysLanguageUid(): array
    {
        trigger_deprecation('in2code/in2studyfinder', '8.0', 'Method "%s()" is deprecated and will be removed in Version 9.0.', __METHOD__);

        return [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ];
    }

    public static function getFullTcaForL10nParent(string $table): array
    {
        return [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'default' => 0,
                'foreign_table' => $table,
                'foreign_table_where' => 'AND ' . $table . '.pid=###CURRENT_PID### AND ' . $table
                    . '.sys_language_uid IN (-1,0)',
            ],
        ];
    }

    public static function getFullTcaForL10nDiffsource(): array
    {
        return [
            'config' => [
                'type' => 'passthrough',
            ],
        ];
    }

    public static function getFullTcaForT3verLabel(): array
    {
        return [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ];
    }

    public static function getFullTcaForHidden(): array
    {
        return [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ];
    }

    public static function getFullTcaForStartTime(): array
    {
        return [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => strtotime('today midnight'),
                ],
            ],
        ];
    }

    public static function getFullTcaForEndTime(): array
    {
        return [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => strtotime('today midnight'),
                ],
            ],
        ];
    }

    public static function getPleaseChooseOption(string $table = ''): array
    {
        $icon = '';

        if ($table !== '') {
            $icon =
                \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName(
                    'EXT:in2studyfinder/Resources/Public/Icons/' . $table . '.png'
                );
        }

        return [
            'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:tca.select.please_choose',
            '',
            $icon,
        ];
    }

    public static function getFullTcaForSingleSelect(
        string $label,
        string $table,
        int $exclude = 1,
        int $minItems = 0,
        string $l10nMode = 'exclude',
        string $l10nDisplay = ''
    ): array {
        return [
            'exclude' => $exclude,
            'l10n_mode' => $l10nMode,
            'l10n_display' => $l10nDisplay,
            'label' => $label,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => $table,
                'foreign_table_where' => 'AND ' . $table . '.sys_language_uid in (-1, 0)',
                'items' => [self::getPleaseChooseOption($table)],
                'minitems' => $minItems,
            ],
        ];
    }

    public static function getFullTcaForSelectCheckBox(
        string $label,
        string $table,
        string $mmTable,
        int $exclude = 1,
        int $minItems = 0,
        int $maxItems = 5,
        string $l10nMode = 'exclude'
    ): array {
        return [
            'exclude' => $exclude,
            'l10n_mode' => $l10nMode,
            'label' => $label,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectCheckBox',
                'foreign_table' => $table,
                'MM' => $mmTable,
                'foreign_table_where' => 'AND ' . $table . '.sys_language_uid in (-1, 0)',
                'minitems' => $minItems,
                'maxitems' => $maxItems,
            ],
        ];
    }

    public static function getFullTcaForSelectSideBySide(
        string $label,
        string $table,
        string $mmTable,
        int $exclude = 1,
        int $minItems = 0,
        int $maxItems = 9999,
        string $l10nMode = 'exclude'
    ) {
        return [
            'exclude' => $exclude,
            'l10n_mode' => $l10nMode,
            'label' => $label,
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => $table,
                'MM' => $mmTable,
                'foreign_table_where' => 'AND ' . $table . '.sys_language_uid in (-1, 0)',
                'minitems' => $minItems,
                'maxitems' => $maxItems,
                'wizards' => [
                    'edit' => self::getEditWizard(),
                    'suggest' => self::getSuggestWizard(),
                ],
            ],
        ];
    }

    public static function getSuggestWizard(): array
    {
        return [
            'type' => 'suggest',
        ];
    }

    public static function getAddWizard(string $table, string $pid = '###CURRENT_PID###'): array
    {
        $wizard = [
            'type' => 'script',
            'title' => 'LLL:EXT:cms/locallang_tca.xlf:sys_template.basedOn_add',
            'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_add.gif',
            'params' => [
                'table' => $table,
                'pid' => $pid,
                'setValue' => 'prepend',
            ],
            'module' => [
                'name' => 'wizard_add',
            ],
        ];

        return $wizard;
    }

    public static function getEditWizard(): array
    {
        $wizard = [
            'type' => 'popup',
            'title' => 'Edit',
            'popup_onlyOpenIfSelected' => 1,
            'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_edit.gif',
            'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
            'module' => [
                'name' => 'wizard_edit',
            ]
        ];

        return $wizard;
    }

    public static function getLinkWizard(
        string $blindLinkOptions = '',
        string $allowedExtensions = '',
        string $blindLinkFields = ''
    ): array {
        $linkWizard = [
            'type' => 'popup',
            'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:header_link_formlabel',
            'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1',
            'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_link.gif',
            'module' => [
                'name' => 'wizard_link',
            ],
            'params' => [
                'blindLinkOptions' => $blindLinkOptions,
                'allowedExtensions' => $allowedExtensions,
                'blindLinkFields' => $blindLinkFields,
            ],
        ];

        return $linkWizard;
    }

    public static function setExtbaseType(
        string $table,
        string $extbaseTypeValue,
        string $insertAfter = 'title',
        int $readOnly = 1
    ): void {
        if (!isset($GLOBALS['TCA'][$table]['ctrl']['type'])) {
            $GLOBALS['TCA'][$table]['ctrl']['type'] = 'tx_extbase_type';
            $extbaseType = [];
            $extbaseType['tx_extbase_type'] = [
                'exclude' => 1,
                'l10n_mode' => 'exclude',
                'label' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:extendedStudycourseLabel',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        [
                            'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:extendedStudycourse',
                            $extbaseTypeValue,
                        ],
                        ['LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:defaultStudycourse', ''],
                    ],
                    'default' => $extbaseTypeValue,
                    'readOnly' => $readOnly,
                    'size' => 1,
                    'maxitems' => 1,
                ],
            ];

            ExtensionManagementUtility::addTCAcolumns($table, $extbaseType);
        }

        ExtensionManagementUtility::addToAllTCAtypes(
            $table,
            $GLOBALS['TCA'][$table]['ctrl']['type'],
            '',
            'after:' . $insertAfter
        );
    }

    public static function addFieldToShowItem(
        string $table,
        string $extbaseType,
        string $field,
        string $insertAfter = 'last'
    ): void {
        $newShowItem = $GLOBALS['TCA'][$table]['types']['0']['showitem'];

        $fieldArray = explode(',', $newShowItem);

        array_walk($fieldArray, [self::class, 'trimValue']);

        if (in_array($insertAfter, $fieldArray)) {
            $arrayKey = array_search($insertAfter, $fieldArray) + 1;
            array_splice($fieldArray, $arrayKey, 0, [$field]);
        } else {
            $fieldArray[] = $field;
            $fieldArray = array_filter($fieldArray);
        }

        $GLOBALS['TCA'][$table]['types'][$extbaseType]['showitem'] = implode(',', $fieldArray);
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public static function addFieldsToPalette(
        string $table,
        string $palette,
        array $fields,
        string $insertAfter = 'last',
        bool $addLineBreakBefore = false,
        bool $addLineBreakAfter = false
    ): void {
        $newShowItem = $GLOBALS['TCA'][$table]['palettes'][$palette]['showitem'];
        $fieldArray = explode(',', $newShowItem);
        $iterator = 0;
        $insertFieldArray = [];

        foreach ($fields as $field) {
            $preLineBreak = '';
            $afterLineBreak = '';

            if ($addLineBreakBefore) {
                $preLineBreak = '--linebreak--,';
            }
            if ($addLineBreakAfter) {
                $afterLineBreak = ',--linebreak--';
            }

            $insertFieldArray[$iterator] = $preLineBreak . $field . $afterLineBreak;

            $iterator++;
        }

        array_walk($fieldArray, [self::class, 'trimValue']);

        if (in_array($insertAfter, $fieldArray, true)) {
            $arrayKey = array_search($insertAfter, $fieldArray, true) + 1;
            array_splice($fieldArray, $arrayKey, 0, $insertFieldArray);
        } else {
            foreach ($insertFieldArray as $value) {
                $fieldArray[] = $value;
            }

            $fieldArray = array_filter($fieldArray);
        }

        $GLOBALS['TCA'][$table]['palettes'][$palette]['showitem'] = implode(',', $fieldArray);
    }

    public static function addFieldsToNewDivForAllTCAtypes(
        string $table,
        string $localLangPath,
        string $localLangId,
        array $fields,
        string $insertAfter
    ): void {
        $fieldString = implode(',', $fields);

        $tab = ', --div--;' . $localLangPath . ':' . $localLangId . ',' . $fieldString . ',';

        ExtensionManagementUtility::addToAllTCAtypes($table, $tab, '', 'after:' . $insertAfter);
    }

    public static function removeFieldsFromTCApalette(
        string $table,
        string $palette,
        array $fields
    ): void {
        self::removeFieldsFromTCA($table, 'palettes', $palette, $fields);
    }


    public static function removeFieldsFromTCAtype(
        string $table,
        string $tcaType,
        array $fields
    ): void {
        self::removeFieldsFromTCA($table, 'types', $tcaType, $fields);
    }

    private static function removeFieldsFromTCA(
        string $table,
        string $section,
        string $sectionName,
        array $fields
    ): void {
        $status = true;

        if ($section !== 'types' && $section !== 'palettes') {
            $status = false;
        }

        if ($status) {
            $showItemArray = explode(',', $GLOBALS['TCA'][$table][$section][$sectionName]['showitem']);

            array_walk($showItemArray, [self::class, 'trimValue']);

            foreach ($fields as $field) {
                if (in_array($field, $showItemArray)) {
                    unset($showItemArray[array_search($field, $showItemArray)]);
                }
            }

            $GLOBALS['TCA'][$table][$section][$sectionName]['showitem'] = implode(',', $showItemArray);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private static function trimValue(string &$value): void
    {
        $value = trim($value);
    }
}

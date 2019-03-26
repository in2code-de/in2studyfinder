<?php
namespace In2code\In2studyfinder\Utility\Hook;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Sebastian Stein <sebastian.stein@in2code.de>, In2code GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Wizard Icon for In2studyfinder
 */
class WizIcon
{
    /**
     * Path to locallang file (with : as postfix)
     *
     * @var string
     */
    protected $locallangPath = 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_db.xlf:';

    /**
     * Processing the wizard items array
     *
     * @param array $wizardItems
     * @return array
     */
    public function proc($wizardItems = array())
    {
        $icon =
            GeneralUtility::getFileAbsFileName(
                'EXT:in2studyfinder/Resources/Public/Icons/Extension.svg'
            );

        $wizardItems['plugins_tx_in2studyfinder_pi1'] = array(
            'icon' => $icon,
            'title' => $GLOBALS['LANG']->sL($this->locallangPath . 'wizardItemTitle'),
            'description' => $GLOBALS['LANG']->sL($this->locallangPath . 'wizardItemDescription'),
            'params' => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=in2studyfinder_pi1',
            'tt_content_defValues' => array(
                'CType' => 'list',
            ),
        );

        return $wizardItems;
    }
}

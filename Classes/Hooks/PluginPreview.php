<?php
namespace In2code\In2studyfinder\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Sebastian Stein <sebastian.stein@in2code.de>, in2code.de
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

use In2code\Powermail\Domain\Model\Form;
use In2code\Powermail\Domain\Repository\MailRepository;
use In2code\Powermail\Utility\ArrayUtility;
use In2code\Powermail\Utility\ConfigurationUtility;
use In2code\Powermail\Utility\ObjectUtility;
use In2code\Powermail\Utility\TemplateUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Service\FlexFormService;

/**
 * Contains a preview rendering for the powermail page module
 */
class PluginPreview implements PageLayoutViewDrawItemHookInterface
{

    /**
     * @var array
     */
    protected $row = [];

    /**
     * @var array
     */
    protected $flexFormData;

    /**
     * @var string
     */
    protected $templatePathAndFile = 'EXT:in2studyfinder/Resources/Private/Templates/StudyCourse/Hook/PluginPreview.html';

    /**
     * Preprocesses the preview rendering of a content element
     *
     * @param PageLayoutView $parentObject Calling parent object
     * @param bool $drawItem Whether to draw the item using the default functionality
     * @param string $headerContent Header content
     * @param string $itemContent Item content
     * @param array $row Record row of tt_content
     * @return void
     */
    public function preProcess(
        PageLayoutView &$parentObject,
        &$drawItem,
        &$headerContent,
        &$itemContent,
        array &$row
    ) {

        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(func_get_args(), __CLASS__ . ' in der Zeile ' . __LINE__);
        $this->initialize($row);
        switch ($this->row['list_type']) {
            case 'in2studyfinder_pi1':
                $drawItem = false;
                $headerContent = '';
                $itemContent = $this->getPluginInformation('Pi1');
                break;
            case 'in2studyfinder_pi2':
                $drawItem = false;
                $headerContent = '';
                $itemContent = $this->getPluginInformation('Pi2');
                break;
            default:
        }
    }

    /**
     * @param string @pluginName
     * @return string
     */
    protected function getPluginInformation($pluginName)
    {
        $standaloneView = TemplateUtility::getDefaultStandAloneView();
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->templatePathAndFile));
        $standaloneView->assignMultiple(
            [
                'row' => $this->row,
                'flexFormData' => $this->flexFormData,
                'pluginName' => $pluginName,
            ]
        );

        switch ($pluginName) {
            case 'Pi1':
                $detailPageRecord = BackendUtility::getRecord('pages', $this->flexFormData['settings']['flexform']['studyCourseDetailPage']);
                $standaloneView->assign('detailPage', $detailPageRecord);
                break;
            case 'Pi2':
                break;
        }

        return $standaloneView->render();
    }

    /**
     * @param array $row
     * @return void
     */
    protected function initialize(array $row)
    {
        $this->row = $row;

        /** @var FlexFormService $flexFormService */
        $flexFormService = ObjectUtility::getObjectManager()->get(FlexFormService::class);
        $this->flexFormData = $flexFormService->convertFlexFormContentToArray($this->row['pi_flexform']);
    }
}

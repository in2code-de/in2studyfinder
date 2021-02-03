<?php

namespace In2code\In2studyfinder\Hooks;

use In2code\In2studyfinder\Utility\AbstractUtility;
use In2code\In2studyfinder\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Contains a preview rendering for the in2studyfinder plugins in the page module
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
    protected $templatePathAndFile = '';

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Preprocesses the preview rendering of a content element
     *
     * @param PageLayoutView $parentObject Calling parent object
     * @param bool $drawItem Whether to draw the item using the default functionality
     * @param string $headerContent Header content
     * @param string $itemContent Item content
     * @param array $row Record row of tt_content
     * @return void
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function preProcess(
        PageLayoutView &$parentObject,
        &$drawItem,
        &$headerContent,
        &$itemContent,
        array &$row
    ) {
        $this->initialize($row);
        $listType = $this->row['list_type'];

        if ($this->isStudyfinderListType($listType)) {
            if (empty($this->settings)) {
                $headerContent = $this->getNoTyposcriptTemplateWarning();
                //$headerContent = '';
            } else {
                switch ($listType) {
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
        }
    }

    /**
     * @param string @pluginName
     * @return string
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getPluginInformation($pluginName)
    {
        $standaloneView = AbstractUtility::getObjectManager()->get(StandaloneView::class);
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
                $detailPageRecord = BackendUtility::getRecord(
                    'pages',
                    $this->flexFormData['settings']['flexform']['studyCourseDetailPage']
                );

                if (!empty($this->flexFormData['settings']['flexform']['select'])) {
                    foreach ($this->flexFormData['settings']['flexform']['select'] as $type => $data) {
                        if ($data !== '') {
                            $data = GeneralUtility::intExplode(',', $data);
                            $standaloneView->assign(
                                $type . 'Array',
                                $this->getTableRecords('tx_in2studyfinder_domain_model_' . $type, $data)
                            );
                        }
                    }
                }

                $standaloneView->assignMultiple(
                    [
                        'detailPage' => $detailPageRecord,
                        'recordStoragePages' => $this->getRecordStoragePages(),
                    ]
                );
                break;
            case 'Pi2':
                $listPageRecord = BackendUtility::getRecord(
                    'pages',
                    $this->flexFormData['settings']['flexform']['studyCourseListPage']
                );

                $standaloneView->assignMultiple(
                    [
                        'listPage' => $listPageRecord,
                    ]
                );

                break;
        }

        return $standaloneView->render();
    }

    /**
     * @param string $table
     * @param array $recordUids
     * @return array
     */
    protected function getTableRecords($table, $recordUids)
    {
        $records = [];
        foreach ($recordUids as $recordUid) {
            $records[] = BackendUtility::getRecord($table, $recordUid);
        }

        return $records;
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.LongVariable)
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getRecordStoragePages()
    {
        $recordStoragePages = [];

        if ($this->row['pages'] !== '') {
            $storageUids = GeneralUtility::trimExplode(',', $this->row['pages'], true);

            foreach ($storageUids as $storageUid) {
                $recordStoragePages[] = BackendUtility::getRecord(
                    'pages',
                    $storageUid
                );
            }
        } else {
            $fullTypoScriptConfiguration = AbstractUtility::getConfigurationManager()->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT,
                'in2studyfinder'
            );
            $storagePid = $fullTypoScriptConfiguration['plugin.']['tx_in2studyfinder.']['settings.']['storagePid'];

            if ($storagePid !== '') {
                $recordStoragePages[] = BackendUtility::getRecord(
                    'pages',
                    $storagePid
                );
            }
        }

        return $recordStoragePages;
    }

    /**
     * @param array $row
     * @return void
     */
    protected function initialize(array $row)
    {
        $this->row = $row;

        $this->settings = ExtensionUtility::getExtensionSettings('in2studyfinder');
        $this->templatePathAndFile = $this->settings['backend']['pluginPreviewTemplate'];

        $flexFormService = ExtensionUtility::getObjectManager()->get(FlexFormService::class);
        $this->flexFormData = $flexFormService->convertFlexFormContentToArray($this->row['pi_flexform']);
    }

    /**
     * @param string $listType
     *
     * @return boolean
     */
    protected function isStudyfinderListType($listType)
    {
        return $listType === 'in2studyfinder_pi1' || $listType === 'in2studyfinder_pi2';
    }

    /**
     * @return string
     */
    protected function getNoTyposcriptTemplateWarning()
    {
        return '<div class="callout callout-danger">
							<h4 class="alert-title">No Typoscript Template!</h4>
							<div class="callout-body">Please include the in2studyfinder TypoScript Template!</div>
				</div>
				';
    }
}

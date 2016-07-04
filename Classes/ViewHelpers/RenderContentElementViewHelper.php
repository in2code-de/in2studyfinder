<?php
namespace In2code\In2studyfinder\ViewHelpers;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class RenderContentElementViewHelper extends AbstractViewHelper {

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var Object
     */
    protected $cObj;

    /**
     * Parse a content element
     *
     * @param int $uid from Content Element
     * @return array
     */
    public function render($uid) {
        $conf = array(
                       'tables' => 'tt_content',
                       'source' => $uid,
                       'dontCheckPid' => 1
        );
        return $this->cObj->RECORDS($conf);
    }

    /**
     * Injects the Configuration Manager
     *
     * @param ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
        $this->configurationManager = $configurationManager;
        $this->cObj = $this->configurationManager->getContentObject();
    }
}

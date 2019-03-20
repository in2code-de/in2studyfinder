<?php
namespace In2code\In2studyfinder\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class RenderContentElementsViewHelper extends AbstractViewHelper
{
    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var Object
     */
    protected $cObj;

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('domainObject', 'mixed', '', true);
        $this->registerArgument('mmTable', 'string', '', true);
    }

    /**
     * Parse content elements from an mm Table
     *
     * @return array
     */
    public function render()
    {
        $domainObject = $this->arguments['domainObject'];
        $mmTable = $this->arguments['mmTable'];
        $language = $GLOBALS['TSFE']->sys_language_uid;

        if ($language > 0) {
            $uid = $domainObject->_getProperty('_localizedUid');
        } else {
            $uid = $domainObject->getUid();
        }

        $uidArray = $this->findTtContentUidsByMmTable($uid, $mmTable);
        $uidList = '';

        if (!empty($uidArray)) {
            foreach ($uidArray as $value) {
                $uidList .= $value['uid'] . ',';
            }

            $uidList = rtrim($uidList, ",");
        }

        $conf = [
            'tables' => 'tt_content',
            'source' => $uidList,
            'dontCheckPid' => 1,
        ];

        return $this->cObj->cObjGetSingle('RECORDS', $conf);
    }

    /**
     * Injects the Configuration Manager
     *
     * @param ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
        $this->cObj = $this->configurationManager->getContentObject();
    }

    public function findTtContentUidsByMmTable($domainObjectUid, $table)
    {

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $uidArray = $queryBuilder
            ->select('uid_foreign as uid')
            ->from($table)
            ->where('uid_local=' . (int)$domainObjectUid)
            ->orderBy('sorting')
            ->addOrderBy('sorting_foreign')
            ->execute()->fetchAll();

        return $uidArray;
    }
}

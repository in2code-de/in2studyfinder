<?php
namespace In2code\In2studyfinder\ViewHelpers;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

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

    /**
     * Parse content elements from an mm Table
     *
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject $domainObject
     * @param string $mmTable
     * @return array
     */
    public function render($domainObject, $mmTable)
    {
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
        $this->cObj = $this->configurationManager->getContentObjectRenderer();
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
            ->addOrderBy('sorting_foreign');

        return $uidArray;
    }
}

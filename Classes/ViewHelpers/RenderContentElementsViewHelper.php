<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use In2code\In2studyfinder\Domain\Model\TtContent;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class RenderContentElementsViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    protected ?ConfigurationManagerInterface $configurationManager = null;

    protected object $cObj;

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('domainObject', 'mixed', '', true);
        $this->registerArgument('mmTable', 'string', '', true);
    }

    public function render(): string
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
            'tables' => TtContent::TABLE,
            'source' => $uidList,
            'dontCheckPid' => 1,
        ];

        return $this->cObj->cObjGetSingle('RECORDS', $conf);
    }

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
        $this->cObj = $this->configurationManager->getContentObject();
    }

    public function findTtContentUidsByMmTable(int $domainObjectUid, string $table)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $uidArray = $queryBuilder
            ->select('uid_foreign as uid')
            ->from($table)
            ->where('uid_local=' . $domainObjectUid)
            ->orderBy('sorting')
            ->addOrderBy('sorting_foreign')
            ->execute()->fetchAll();

        return $uidArray;
    }
}

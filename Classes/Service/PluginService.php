<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Service;

use In2code\In2studyfinder\Utility\PageUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PluginService extends AbstractService
{
    public function getPluginStoragePids(array $pluginRecord): array
    {
        $storagePids = [];

        if (array_key_exists('pages', $pluginRecord) && $pluginRecord['pages'] !== '') {
            $storagePids = GeneralUtility::intExplode(',', $pluginRecord['pages']);

            // add recursive pids if recursive is set in the plugin
            if (array_key_exists('recursive', $pluginRecord) && $pluginRecord['recursive'] > 0) {
                $recursiveStoragePids = '';
                foreach ($storagePids as $storagePid) {
                    $recursiveStoragePids .=
                        PageUtility::getTreeList($storagePid, $pluginRecord['recursive'], 0, '1') . ',';
                }

                $storagePids = GeneralUtility::trimExplode(',', $recursiveStoragePids, true);
            }
        }

        return $storagePids;
    }

    /**
     * prepares filter restrictions which are set in the plugin flexform
     */
    public function preparePluginRestrictions(array $flexFormRestrictions, array $filter): array
    {
        $restrictions = [];
        if (!empty($flexFormRestrictions)) {
            foreach ($flexFormRestrictions as $filterName => $uid) {
                if ($uid !== '' && array_key_exists($filterName, $filter)) {
                    $restrictions[$filterName] = GeneralUtility::intExplode(',', $uid, true);
                } else {
                    $this->logger->info(
                        'Remove the plugin filter restriction for filter: "' . $filterName .
                        '". Because the given restriction is not defined in the typoscript filter section.',
                        ['additionalInfo' => ['class' => self::class, 'method' => __METHOD__, 'line' => __LINE__]]
                    );
                }
            }
        }

        return $restrictions;
    }
}

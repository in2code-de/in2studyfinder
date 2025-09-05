<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Service;

use Exception;
use In2code\In2studyfinder\Exception\MissingApiKeyException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationService
{
    public function getMistralApiKey(): string
    {
        return (string)$this->getExtensionConfiguration('mistralApiKey');
    }

    /**
     * @throws Exception
     */
    public function getDetailPid(): int
    {
        $pid = (int)$this->getExtensionConfiguration('detailPid');

        if ($pid === 0) {
            throw new Exception(
                'Detail PID not found. Please configure it in the extension settings.',
                1750159320
            );
        }

        return $pid;
    }

    private function getExtensionConfiguration(string $key)
    {
        try {
            return GeneralUtility::makeInstance(ExtensionConfiguration::class)
                ->get('in2studyfinder', $key);
        } catch (\Exception $e) {
            return null;
        }
    }
}

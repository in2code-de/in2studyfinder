<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\Ai\Service;

use Exception;
use In2code\In2studyfinder\Ai\Exception\MissingApiKeyException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationService
{
    /**
     * @throws MissingApiKeyException
     */
    public function getMistralApiKey(): string
    {
        $apiKey = (string)$this->getExtensionConfiguration('mistralApiKey');

        if (empty($apiKey)) {
            throw new MissingApiKeyException(
                'Mistral API key is not set. Please configure it in the extension settings.',
                1749650734
            );
        }

        return $apiKey;
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

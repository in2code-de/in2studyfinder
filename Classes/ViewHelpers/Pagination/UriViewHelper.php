<?php

declare(strict_types=1);

namespace In2code\In2studyfinder\ViewHelpers\Pagination;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class UriViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * Constructor
     */
    public function __construct(private readonly \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder)
    {
        parent::__construct();
    }
    /**
     * Initialize arguments
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('name', 'string', 'identifier important if more widgets on same page', false, 'widget');
        $this->registerArgument('arguments', 'array', 'Arguments', false, []);
    }

    /**
     * Build an uri to current action with &tx_ext_plugin[currentPage]=2
     *
     * @return string The rendered uri
     */
    public function render(): string
    {
        $uriBuilder = $this->uriBuilder;
        $uriBuilder->setRequest($this->renderingContext->getRequest());

        $extensionName = $this->renderingContext->getRequest()->getControllerExtensionName();
        $pluginName = $this->renderingContext->getRequest()->getPluginName();
        $extensionService = GeneralUtility::makeInstance(ExtensionService::class);
        $pluginNamespace = $extensionService->getPluginNamespace($extensionName, $pluginName);
        $argumentPrefix = $pluginNamespace . '[' . $this->arguments['name'] . ']';
        $arguments = $this->hasArgument('arguments') ? $this->arguments['arguments'] : [];
        if ($this->hasArgument('action')) {
            $arguments['action'] = $this->arguments['action'];
        }

        if ($this->hasArgument('format') && $this->arguments['format'] !== '') {
            $arguments['format'] = $this->arguments['format'];
        }

        $uriBuilder->reset()
            ->setArguments([$argumentPrefix => $arguments])
            ->setAddQueryString(true)
            ->setArgumentsToBeExcludedFromQueryString([$argumentPrefix, 'cHash']);
        $addQueryStringMethod = $this->arguments['addQueryStringMethod'] ?? null;
        if (is_string($addQueryStringMethod)) {
            $uriBuilder->setAddQueryStringMethod($addQueryStringMethod);
        }

        return $uriBuilder->build();
    }
}

<?php

use In2code\In2studyfinder\Controller\BackendController;
use In2code\In2studyfinder\Utility\ConfigurationUtility;

if (ConfigurationUtility::isBackendModuleEnabled()) {
    return [
        'web_in2studyfinder' => [
            'parent' => 'tools',
            'position' => ['bottom'],
            'access' => 'user',
            'workspaces' => '*',
            'path' => '/module/page/example', // ?
            'labels' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_mod.xlf',
            'iconIdentifier' => 'in2studyfinder-plugin-icon',
            'extensionName' => 'In2studyfinder',
            'controllerActions' => [
                BackendController::class => [
                    'list',
                    'export',
                ],
            ],
        ],
    ];
}

<?php

return [
    'web_in2studyfinder_chatlog' => [
        'parent' => 'web',
        'position' => ['after' => 'web_info'],
        'access' => 'user',
        'workspaces' => 'live',
        'path' => '/module/web/in2studyfinder/chatlog',
        'iconIdentifier' => 'in2studyfinder-plugin-icon',
        'labels' => 'LLL:EXT:in2studyfinder/Resources/Private/Language/locallang_mod_chatlog.xlf',
        'extensionName' => 'In2studyfinder',
        'navigationComponent' => '@typo3/backend/tree/page-tree-element',
        'controllerActions' => [
            \In2code\In2studyfinder\Controller\Backend\ChatLogController::class => [
                'list',
                'show',
            ],
        ],
    ],
];

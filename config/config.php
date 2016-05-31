<?php

return [
    'compactation' => [
        'enabled' => false,
        'compactor' => '\\Mcustiel\\CompactPages\\Classes\\Services\\Implementation\\BasicHtmlCompactor',
    ],
    'inline_assets' => [
        'overwrite-native' => [
            'styles'  => false,
            'scripts' => false,
        ],
    ],
];

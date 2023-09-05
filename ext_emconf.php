<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'sf_event_mgt multidates',
    'description' => 'Multidates for sf_event_mgt',
    'category' => 'be',
    'author' => 'Volker Golbig',
    'author_email' => 'v.golbig@machwert.de',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'version' => '0.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'php' => '8.0.0-8.2.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

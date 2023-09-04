<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'sf_event_mgt multidates',
    'description' => 'Multidates for sf_event_mgt',
    'category' => 'be',
    'author' => 'Volker Golbig',
    'author_email' => 'v.golbig@machwert.de',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'version' => '0.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-12.99.99',
            'php' => '8.1.0-8.2.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'sf_event_mgt multidates',
    'description' => 'Enables multiple dates for sf_event_mgt events. Calender view of sf_event_mgt is adapted to display those events.',
    'category' => 'be',
    'author' => 'Volker Golbig',
    'author_email' => 'v.golbig@machwert.de',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'version' => '2.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'php' => '8.2.0-8.99.99',
            'sf_event_mgt' => '8.6.0-8.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

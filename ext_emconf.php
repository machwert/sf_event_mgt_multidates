<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'sf_event_mgt multidates',
    'description' => 'Enables multiple dates for sf_event_mgt events. Calender view of sf_event_mgt is adapted to display those events.',
    'category' => 'be',
    'author' => 'Volker Golbig',
    'author_email' => 'v.golbig@machwert.de',
    'state' => 'beta',
    'clearCacheOnLoad' => 1,
    'version' => '0.0.6',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'php' => '8.0.0-8.2.99',
            'sf_event_mgt' => '7.2.0-7.3.3',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

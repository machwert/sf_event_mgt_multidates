<?php

return [
    'ctrl' => [
        'title' => 'Startdates',
        'label' => 'startdates',
        'label_alt' => 'startdatetime',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        // Muss true sein: die Tabelle wird als Inline-Kind von
        // tx_sfeventmgt_domain_model_event.startdates verwendet, und diese
        // Tabelle ist workspace-faehig. TYPO3 migrierte das bisher automatisch.
        'versioningWS' => true,
        'hideTable' => false,
        'origUid' => 't3_origuid',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'startdatetime' => 'startdatetime',
        ],
        'typeicon_classes' => [
            'default' => 'ext-sfeventmgtmultidates-default',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => 'startdatetime',
        ],
    ],
    'palettes' => [
        'timeRestriction' => ['showitem' => 'startdatetime'],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
                'items' => [
                    [
                        'label' => '',
                        'invertStateDisplay' => false,
                    ],
                ],
            ],
        ],
        'startdatetime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'event' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];

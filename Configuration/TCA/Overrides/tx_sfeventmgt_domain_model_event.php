<?php

defined('TYPO3') or die();

$newTableColumns = [
    'eventduration' => [
        'exclude' => true,
        'label' => 'Event Dauer (Sekunden)',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim',
        ],
    ],
    'startdates' => [
        'exclude' => true,
        'label' => 'Startdates',
        'config' => [
            'type' => 'inline',
            'foreign_table' => 'tx_sfeventmgtmultidates_domain_model_startdates',
            'foreign_label' => 'Startdaten',
            'foreign_field' => 'event',
            'foreign_default_sortby' => 'startdatetime ASC',
            'maxitems' => 100,
            'appearance' => [
                'expandSingle' => 1,
                'levelLinksPosition' => 'bottom',
                'showSynchronizationLink' => 1,
                'showPossibleLocalizationRecords' => 1,
                'useSortable' => 0,
            ],
        ],
    ],
];


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_sfeventmgt_domain_model_event', $newTableColumns);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_sfeventmgt_domain_model_event',
    'startdates,eventduration',
    '',
    'after:startdate'
);
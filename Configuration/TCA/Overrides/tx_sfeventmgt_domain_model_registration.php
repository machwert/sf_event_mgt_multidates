<?php

$newTableColumns = [
    'startdatetime' => [
        'exclude' => true,
        'label' => 'Startdatum',
        'config' => [
            'type' => 'datetime',
            'default' => 0,
        ],
    ],
];


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_sfeventmgt_domain_model_registration', $newTableColumns);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_sfeventmgt_domain_model_registration',
    'startdatetime',
    '',
    'after:title'
);

<?php
declare(strict_types=1);

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\DERHANSEN\SfEventMgt\Domain\Model\Event::class] = [
    'className' => \Machwert\SfEventMgtMultidates\Domain\Model\Event::class,
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\DERHANSEN\SfEventMgt\Domain\Model\Registration::class] = [
    'className' => \Machwert\SfEventMgtMultidates\Domain\Model\Registration::class,
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\DERHANSEN\SfEventMgt\Controller\EventController::class] = [
    'className' => \Machwert\SfEventMgtMultidates\Xclass\NewEventController::class
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\DERHANSEN\SfEventMgt\Service\RegistrationService::class] = [
    'className' => \Machwert\SfEventMgtMultidates\Xclass\NewRegistrationService::class
];

/*
 * for TYPO3 v10 and v11:
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
    ->registerImplementation(
        \DERHANSEN\SfEventMgt\Domain\Model\Event::class,
        \Machwert\SfEventMgtMultidates\Domain\Model\Event::class
    );
*/

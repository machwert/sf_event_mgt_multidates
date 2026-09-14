<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "sf_event_mgt_multidates" for TYPO3 CMS which extends "sf_event_mgt".
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Machwert\SfEventMgtMultidates\Xclass;

use DateTime;
use DERHANSEN\SfEventMgt\Domain\Model\Dto\CategoryDemand;
use DERHANSEN\SfEventMgt\Domain\Model\Dto\EventDemand;
use DERHANSEN\SfEventMgt\Domain\Model\Dto\ForeignRecordDemand;
use DERHANSEN\SfEventMgt\Domain\Model\Event;
use DERHANSEN\SfEventMgt\Domain\Model\Registration;
use DERHANSEN\SfEventMgt\Event\ModifyCalendarViewVariablesEvent;
use DERHANSEN\SfEventMgt\Utility\RegistrationResult;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Annotation as Extbase;

class NewEventController extends \DERHANSEN\SfEventMgt\Controller\EventController
{
    /**
     * Initializes the current action
     */
    public function initializeAction(): void
    {
        $getVars = $this->request->getQueryParams()['tx_sfeventmgt_pieventdetail'] ?? $this->request->getQueryParams()['tx_sfeventmgt_pieventregistration'] ?? null;

        if (isset($getVars['event'])) {
            $eventId = (int) $getVars['event'];
            if ($eventId > 0) {
                $this->settings['singleEvent'] = $eventId;
            }
        }
        parent::initializeAction();
    }

    /**
     * Calendar view
     *
     * @param array<string, mixed> $overwriteDemand
     */
    public function calendarAction(array $overwriteDemand = []): ResponseInterface
    {
        $eventDemand = EventDemand::createFromSettings($this->settings);
        $foreignRecordDemand = ForeignRecordDemand::createFromSettings($this->settings);
        $categoryDemand = CategoryDemand::createFromSettings($this->settings);
        if ($this->isOverwriteDemand($overwriteDemand)) {
            $eventDemand = $this->overwriteEventDemandObject($eventDemand, $overwriteDemand);
        }

        // Set month/year to demand if not given
        if (!$eventDemand->getMonth()) {
            $currentMonth = (int)date('n');
            $eventDemand->setMonth($currentMonth);
        } else {
            $currentMonth = $eventDemand->getMonth();
        }
        if (!$eventDemand->getYear()) {
            $currentYear = (int)date('Y');
            $eventDemand->setYear($currentYear);
        } else {
            $currentYear = $eventDemand->getYear();
        }

        // If a weeknumber is given in overwriteDemand['week'], we overwrite the current month
        if ($overwriteDemand['week'] ?? false) {
            $firstDayOfWeek = (new DateTime())->setISODate($currentYear, (int)$overwriteDemand['week']);
            // Kalenderwoche 1 liegt teils im Dezember - Monat UND Jahr nachziehen,
            // sonst rechnet der Kalender zum Jahreswechsel mit dem falschen Jahr.
            $currentMonth = (int)$firstDayOfWeek->format('n');
            $currentYear = (int)$firstDayOfWeek->format('Y');
            $eventDemand->setMonth($currentMonth);
            $eventDemand->setYear($currentYear);
        } else {
            // date('o') ist das ISO-Jahr und gehoert zu date('W').
            $firstDayOfWeek = (new DateTime())->setISODate((int)date('o'), (int)date('W'));
        }

        // Set demand from calendar date range instead of month / year
        if ((bool)($this->settings['calendar']['includeEventsForEveryDayOfAllCalendarWeeks'] ?? false)) {
            $eventDemand = $this->changeEventDemandToFullMonthDateRange($eventDemand);
        }

        $firstEventsFromTimestamp = strtotime("1 August 2023");
        $firstEventsFromDatetime = (new DateTime())->setTimestamp($firstEventsFromTimestamp);
        $searchDemand = $eventDemand->getSearchDemand();
        if ($searchDemand !== null) {
            $searchDemand->setStartDate($firstEventsFromDatetime);
        }
        $events = $this->eventRepository->findDemanded($eventDemand);

        $eventCount = $events->count();
        $i = $eventCount;
        for ($j = 0; $j < $eventCount; $j++) {
            $startDates = $events[$j]->getStartdates();
            $eventDuration = $events[$j]->getEventduration();
            if (!empty($startDates)) {
                foreach ($startDates as $key2 => $startDate) {
                    $events[$i] = clone $events[$j];
                    $events[$i]->setStartDate($startDate->getStartdatetime());
                    $endTimeStamp = $startDate->getStartdatetime()->getTimestamp();
                    $endDateTime = (new DateTime())->setTimestamp($endTimeStamp + $eventDuration);
                    $events[$i]->setEndDate($endDateTime);
                    $i++;
                }
            }
        }

        $weeks = $this->calendarService->getCalendarArray(
            $currentMonth,
            $currentYear,
            strtotime('today midnight'),
            (int)($this->settings['calendar']['firstDayOfWeek'] ?? 1),
            $events
        );

        // TYPO3 v13: TSFE ist nicht mehr ueber getTypoScriptFrontendController()
        // erreichbar; die Seiten-ID kommt aus dem Request-Attribut.
        $currentPageId = $this->getFrontendPageInformation()->getId();

        $modifyCalendarViewVariablesEvent = new ModifyCalendarViewVariablesEvent(
            [
                'events' => $events,
                'weeks' => $weeks,
                'categories' => $this->categoryRepository->findDemanded($categoryDemand),
                'locations' => $this->locationRepository->findDemanded($foreignRecordDemand),
                'organisators' => $this->organisatorRepository->findDemanded($foreignRecordDemand),
                'eventDemand' => $eventDemand,
                'overwriteDemand' => $overwriteDemand,
                'currentPageId' => $currentPageId,
                'firstDayOfMonth' => DateTime::createFromFormat(
                    'd.m.Y',
                    sprintf('1.%s.%s', $currentMonth, $currentYear)
                ),
                'previousMonthConfig' => $this->calendarService->getDateConfig($currentMonth, $currentYear, '-1 month'),
                'nextMonthConfig' => $this->calendarService->getDateConfig($currentMonth, $currentYear, '+1 month'),
                'weekConfig' => $this->calendarService->getWeekConfig($firstDayOfWeek),
                'settings' => $this->settings,
            ],
            $this,
            $this->request
        );
        $this->eventDispatcher->dispatch($modifyCalendarViewVariablesEvent);
        $variables = $modifyCalendarViewVariablesEvent->getVariables();

        $this->view->assignMultiple($variables);

        // Ohne diese Tags wird die Kalenderseite beim Bearbeiten einer
        // Veranstaltung nicht verworfen und zeigt weiter den alten Stand.
        $cacheDataCollector = $this->request->getAttribute('frontend.cache.collector');
        $this->eventCacheService->addPageCacheTagsByEventDemandObject($cacheDataCollector, $eventDemand);

        return $this->htmlResponse();
    }
}

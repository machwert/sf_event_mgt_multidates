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
use DERHANSEN\SfEventMgt\Event\ModifyCalendarViewVariablesEvent;
use DERHANSEN\SfEventMgt\Utility\RegistrationResult;
use Psr\Http\Message\ResponseInterface;

class NewEventController extends \DERHANSEN\SfEventMgt\Controller\EventController
{

    /**
     * Initializes the current action
     */
    public function initializeAction(): void
    {
        $getVars = $this->request->getParsedBody()['tx_sfeventmgt_pieventdetail'] ?? $this->request->getQueryParams()['tx_sfeventmgt_pieventdetail'] ?? null;
        if(!isset($getVars['event'])) {
            $getVars = $this->request->getParsedBody()['tx_sfeventmgt_pieventregistration'] ?? $this->request->getQueryParams()['tx_sfeventmgt_pieventregistration'] ?? null;
        }
        if(isset($getVars['event'])) {
            $eventId = (int) $getVars['event'];
            if ($eventId > 0) {
                $this->settings['singleEvent'] = $eventId;
                $this->settings['disableOverrideDemand'] = 0;
            }
        }
        parent::initializeAction();
    }

    /**
     * Calendar view
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
            $currentMonth = (int)$firstDayOfWeek->format('m');
            $eventDemand->setMonth($currentMonth);
        } else {
            $firstDayOfWeek = (new DateTime())->setISODate($currentYear, (int)date('W'));
        }

        // Set demand from calendar date range instead of month / year
        if ((bool)($this->settings['calendar']['includeEventsForEveryDayOfAllCalendarWeeks'] ?? false)) {
            $eventDemand = $this->changeEventDemandToFullMonthDateRange($eventDemand);
        }

        $firstEventsFromTimestamp = strtotime("1 August 2023");
        $firstEventsFromDatetime = (new DateTime())->setTimestamp($firstEventsFromTimestamp);
        $eventDemand->getSearchDemand()->setStartDate($firstEventsFromDatetime);
        $events = $this->eventRepository->findDemanded($eventDemand);

        $eventCount = $events->count();
        $i = $eventCount;
        for($j = 0; $j < $eventCount; $j++) {
            $startDates = $events[$j]->getStartdates();
            $eventDuration = $events[$j]->getEventduration();
            if(!empty($startDates)) {
                foreach($startDates as $key2 => $startDate) {
                    $events[$i] = clone $events[$j];
                    $events[$i]->setStartDate($startDate->getStartdatetime());
                    $endTimeStamp = $startDate->getStartdatetime()->getTimestamp();
                    $endDateTime = (new DateTime())->setTimestamp($endTimeStamp+$eventDuration);
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

        $modifyCalendarViewVariablesEvent = new ModifyCalendarViewVariablesEvent(
            [
                'events' => $events,
                'weeks' => $weeks,
                'categories' => $this->categoryRepository->findDemanded($categoryDemand),
                'locations' => $this->locationRepository->findDemanded($foreignRecordDemand),
                'organisators' => $this->organisatorRepository->findDemanded($foreignRecordDemand),
                'eventDemand' => $eventDemand,
                'overwriteDemand' => $overwriteDemand,
                'currentPageId' => $this->getTypoScriptFrontendController()->id,
                'firstDayOfMonth' => DateTime::createFromFormat(
                    'd.m.Y',
                    sprintf('1.%s.%s', $currentMonth, $currentYear)
                ),
                'previousMonthConfig' => $this->calendarService->getDateConfig($currentMonth, $currentYear, '-1 month'),
                'nextMonthConfig' => $this->calendarService->getDateConfig($currentMonth, $currentYear, '+1 month'),
                'weekConfig' => $this->calendarService->getWeekConfig($firstDayOfWeek),
                'settings' => $this->settings,
            ],
            $this
        );
        $this->eventDispatcher->dispatch($modifyCalendarViewVariablesEvent);
        $variables = $modifyCalendarViewVariablesEvent->getVariables();

        $this->view->assignMultiple($variables);
        return $this->htmlResponse();
    }
}

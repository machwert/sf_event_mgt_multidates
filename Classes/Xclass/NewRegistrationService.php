<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "sf_event_mgt_multidates" for TYPO3 CMS which extends "sf_event_mgt".
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Machwert\SfEventMgtMultidates\Xclass;

use DERHANSEN\SfEventMgt\Domain\Model\Event;
use DERHANSEN\SfEventMgt\Domain\Model\Registration;
use DERHANSEN\SfEventMgt\Event\ModifyCheckRegistrationSuccessEvent;
use DERHANSEN\SfEventMgt\Utility\RegistrationResult;

class NewRegistrationService extends \DERHANSEN\SfEventMgt\Service\RegistrationService
{
    /**
     * Checks, if the registration can successfully be created.
     *
     * Anders als die Originalmethode prueft diese Variante weder
     * registrationDeadline noch start-/enddate, damit die Anmeldung bei
     * Veranstaltungen mit mehreren Terminen nicht am ersten Termin scheitert.
     *
     * @return array{0: bool, 1: int}
     */
    public function checkRegistrationSuccess(Event $event, Registration $registration): array
    {
        $result = RegistrationResult::REGISTRATION_SUCCESSFUL;
        $success = true;
        $registrations = $event->getRegistrations();
        if ($event->getEnableRegistration() === false) {
            $success = false;
            $result = RegistrationResult::REGISTRATION_NOT_ENABLED;
        } elseif ($registrations !== null && $registrations->count() >= $event->getMaxParticipants()
            && $event->getMaxParticipants() > 0 && !$event->getEnableWaitlist()
        ) {
            $success = false;
            $result = RegistrationResult::REGISTRATION_FAILED_MAX_PARTICIPANTS;
        } elseif ($event->getFreePlaces() < $registration->getAmountOfRegistrations()
            && $event->getMaxParticipants() > 0 && !$event->getEnableWaitlist()
        ) {
            $success = false;
            $result = RegistrationResult::REGISTRATION_FAILED_NOT_ENOUGH_FREE_PLACES;
        } elseif ($event->getMaxRegistrationsPerUser() < $registration->getAmountOfRegistrations()) {
            $success = false;
            $result = RegistrationResult::REGISTRATION_FAILED_MAX_AMOUNT_REGISTRATIONS_EXCEEDED;
        } elseif ($event->getUniqueEmailCheck() &&
            $this->emailNotUnique($event, $registration->getEmail())
        ) {
            $success = false;
            $result = RegistrationResult::REGISTRATION_FAILED_EMAIL_NOT_UNIQUE;
        } elseif ($registrations !== null && $registrations->count() >= $event->getMaxParticipants()
            && $event->getMaxParticipants() > 0 && $event->getEnableWaitlist()
        ) {
            $result = RegistrationResult::REGISTRATION_SUCCESSFUL_WAITLIST;
        }

        $modifyCheckRegistrationSuccessEvent = new ModifyCheckRegistrationSuccessEvent(
            $success,
            $result,
            $registration
        );
        $this->eventDispatcher->dispatch($modifyCheckRegistrationSuccessEvent);

        return [$modifyCheckRegistrationSuccessEvent->getSuccess(), $modifyCheckRegistrationSuccessEvent->getResult()];
    }
}

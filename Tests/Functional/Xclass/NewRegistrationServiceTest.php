<?php

declare(strict_types=1);

namespace Machwert\SfEventMgtMultidates\Tests\Functional\Xclass;

use DateTime;
use DERHANSEN\SfEventMgt\Domain\Model\Event;
use DERHANSEN\SfEventMgt\Domain\Model\Registration;
use DERHANSEN\SfEventMgt\Service\RegistrationService;
use DERHANSEN\SfEventMgt\Utility\RegistrationResult;
use Machwert\SfEventMgtMultidates\Xclass\NewRegistrationService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Haelt die Anmelderegeln von machwert.de fest.
 *
 * Der XCLASS ersetzt checkRegistrationSuccess() aus sf_event_mgt und laesst
 * dabei bewusst alle Datumspruefungen weg, damit eine Veranstaltung mit
 * mehreren Terminen nicht am ersten Termin scheitert. Die Faelle
 * "vergangene Veranstaltung", "abgelaufener Anmeldeschluss" und
 * "Enddatum vorbei" sind deshalb KEIN Fehler, sondern die Absicht - und
 * stehen hier, damit diese Entscheidung dokumentiert ist statt implizit.
 *
 * Functional statt Unit, weil der Dienst sieben Abhaengigkeiten im
 * Konstruktor hat, Event::getRegistrations() den Context aus dem Container
 * zieht und emailNotUnique() die Datenbank befragt.
 */
final class NewRegistrationServiceTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'derhansen/sf_event_mgt',
        'machwert/sf_event_mgt_multidates',
    ];

    private RegistrationService $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->get(RegistrationService::class);
    }

    /**
     * Ohne diese Zusicherung testen alle folgenden Faelle die Originalklasse
     * und waeren wertlos.
     */
    #[Test]
    public function derXclassIstUeberhauptAktiv(): void
    {
        self::assertInstanceOf(NewRegistrationService::class, $this->subject);
    }

    /**
     * @param array<string, mixed> $eventDaten
     */
    #[Test]
    #[DataProvider('anmelderegelnProvider')]
    public function anmeldungWirdWieFestgelegtBeurteilt(
        array $eventDaten,
        int $angefragtePlaetze,
        bool $erwarteterErfolg,
        int $erwartetesErgebnis,
    ): void {
        $event = $this->buildEvent($eventDaten);

        $registration = new Registration();
        $registration->setAmountOfRegistrations($angefragtePlaetze);
        $registration->setEmail('teilnehmer@example.org');

        [$erfolg, $ergebnis] = $this->subject->checkRegistrationSuccess($event, $registration);

        self::assertSame($erwarteterErfolg, $erfolg, 'Erfolgsflag weicht ab');
        self::assertSame($erwartetesErgebnis, $ergebnis, 'Ergebniscode weicht ab');
    }

    /**
     * @return array<string, array{0: array<string, mixed>, 1: int, 2: bool, 3: int}>
     */
    public static function anmelderegelnProvider(): array
    {
        return [
            // --- Normalfaelle ---
            'freie plaetze vorhanden' => [
                ['maxParticipants' => 10, 'anzahlAnmeldungen' => 3],
                1, true, RegistrationResult::REGISTRATION_SUCCESSFUL,
            ],
            'unbegrenzte teilnehmerzahl' => [
                ['maxParticipants' => 0, 'anzahlAnmeldungen' => 999],
                1, true, RegistrationResult::REGISTRATION_SUCCESSFUL,
            ],

            // --- Sperren ---
            'anmeldung abgeschaltet' => [
                ['enableRegistration' => false, 'maxParticipants' => 10],
                1, false, RegistrationResult::REGISTRATION_NOT_ENABLED,
            ],
            'ausgebucht ohne warteliste' => [
                ['maxParticipants' => 10, 'anzahlAnmeldungen' => 10],
                1, false, RegistrationResult::REGISTRATION_FAILED_MAX_PARTICIPANTS,
            ],
            'zu wenig freie plaetze fuer mehrfachanmeldung' => [
                ['maxParticipants' => 10, 'anzahlAnmeldungen' => 8, 'maxRegistrationsPerUser' => 5],
                3, false, RegistrationResult::REGISTRATION_FAILED_NOT_ENOUGH_FREE_PLACES,
            ],
            'mehr plaetze als pro person erlaubt' => [
                ['maxParticipants' => 100, 'anzahlAnmeldungen' => 0, 'maxRegistrationsPerUser' => 2],
                3, false, RegistrationResult::REGISTRATION_FAILED_MAX_AMOUNT_REGISTRATIONS_EXCEEDED,
            ],

            // --- Warteliste ---
            'ausgebucht mit warteliste' => [
                ['maxParticipants' => 10, 'anzahlAnmeldungen' => 10, 'enableWaitlist' => true],
                1, true, RegistrationResult::REGISTRATION_SUCCESSFUL_WAITLIST,
            ],

            // --- Bewusste Abweichung vom Original: keine Datumspruefungen ---
            // Das Original wuerde hier REGISTRATION_FAILED_EVENT_EXPIRED liefern.
            'vergangene veranstaltung bleibt buchbar' => [
                ['maxParticipants' => 10, 'anzahlAnmeldungen' => 0, 'startdate' => '-10 days'],
                1, true, RegistrationResult::REGISTRATION_SUCCESSFUL,
            ],
            // Das Original wuerde hier REGISTRATION_FAILED_DEADLINE_EXPIRED liefern.
            'abgelaufener anmeldeschluss bleibt buchbar' => [
                ['maxParticipants' => 10, 'anzahlAnmeldungen' => 0, 'registrationDeadline' => '-1 day'],
                1, true, RegistrationResult::REGISTRATION_SUCCESSFUL,
            ],
            // Diese Pruefung (REGISTRATION_FAILED_EVENT_ENDED) kam erst nach
            // sf_event_mgt 7.3.3 dazu und wurde nie uebernommen. Die Checkbox
            // "Anmeldung bis Veranstaltungsende" im Backend bleibt damit ohne
            // Wirkung - hier festgehalten, damit es eine Entscheidung ist.
            'enddatum vorbei bleibt buchbar' => [
                [
                    'maxParticipants' => 10,
                    'anzahlAnmeldungen' => 0,
                    'allowRegistrationUntilEnddate' => true,
                    'startdate' => '-10 days',
                    'enddate' => '-9 days',
                ],
                1, true, RegistrationResult::REGISTRATION_SUCCESSFUL,
            ],
        ];
    }

    /**
     * Dieser Fall braucht die Datenbank: emailNotUnique() setzt ein eigenes
     * SELECT auf tx_sfeventmgt_domain_model_registration ab.
     */
    #[Test]
    public function bereitsVergebeneEmailWirdAbgelehnt(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Registrations.csv');

        $event = $this->buildEvent(['maxParticipants' => 10, 'uniqueEmailCheck' => true]);
        $event->_setProperty('uid', 1);

        $registration = new Registration();
        $registration->setAmountOfRegistrations(1);
        $registration->setEmail('bereits@example.org');

        [$erfolg, $ergebnis] = $this->subject->checkRegistrationSuccess($event, $registration);

        self::assertFalse($erfolg);
        self::assertSame(RegistrationResult::REGISTRATION_FAILED_EMAIL_NOT_UNIQUE, $ergebnis);
    }

    #[Test]
    public function neueEmailWirdAkzeptiert(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Registrations.csv');

        $event = $this->buildEvent(['maxParticipants' => 10, 'uniqueEmailCheck' => true]);
        $event->_setProperty('uid', 1);

        $registration = new Registration();
        $registration->setAmountOfRegistrations(1);
        $registration->setEmail('neu@example.org');

        [$erfolg, $ergebnis] = $this->subject->checkRegistrationSuccess($event, $registration);

        self::assertTrue($erfolg);
        self::assertSame(RegistrationResult::REGISTRATION_SUCCESSFUL, $ergebnis);
    }

    /**
     * @param array<string, mixed> $daten
     */
    private function buildEvent(array $daten): Event
    {
        $event = new Event();
        $event->setEnableRegistration($daten['enableRegistration'] ?? true);
        $event->setMaxParticipants($daten['maxParticipants'] ?? 0);
        $event->setEnableWaitlist($daten['enableWaitlist'] ?? false);
        $event->setMaxRegistrationsPerUser($daten['maxRegistrationsPerUser'] ?? 1);
        $event->setUniqueEmailCheck($daten['uniqueEmailCheck'] ?? false);
        $event->setAllowRegistrationUntilEnddate($daten['allowRegistrationUntilEnddate'] ?? false);

        foreach (['startdate' => 'setStartdate', 'enddate' => 'setEnddate', 'registrationDeadline' => 'setRegistrationDeadline'] as $key => $setter) {
            if (isset($daten[$key])) {
                $event->{$setter}(new DateTime($daten[$key]));
            }
        }

        for ($i = 0; $i < ($daten['anzahlAnmeldungen'] ?? 0); $i++) {
            $event->addRegistration(new Registration());
        }

        return $event;
    }
}

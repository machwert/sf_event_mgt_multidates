# sf_event_mgt_multidates TYPO3 Extension extends sf_event_mgt

Enables multiple dates for sf_event_mgt events
- Adapted calender view
- Can be configured to send FluidEmails
- Can be configured to show registration forms in detail view
- Uses Xclasses (see ext_localconf.php) to extend sf_event_mgt_multidates
- Attention: For new registrations the date checks are removed - 'deadline expired', 'event expired'
  and 'event ended' (`sf_event_mgt/Classes/Service/RegistrationService.php::checkRegistrationSuccess`
  is Xclassed). This is intentional: with multiple dates a registration must not fail because the
  first date has passed. See [Tests](#tests) - the behaviour is pinned down there.
- Most probably you need to adjust the shipped fluid templates in sf_event_mgt_multidates/Resources/Private/Extension/sf_event_mgt/.. to your need

## Version matrix

| Extension | TYPO3  | sf_event_mgt | PHP   | Branch |
|-----------|--------|--------------|-------|--------|
| 3.x       | 14.3   | ^9.0         | ^8.3  | `main` |
| **2.x**   | 13.4   | ^8.6         | ^8.2  | `13.4` |

The major version follows the extension's own semantic versioning, not the TYPO3 major.
Which TYPO3 version a release supports is declared in its `composer.json`, never guessed
from the version number.

## Installation

1.
Composer installation:

    composer req machwert/sf_event_mgt_multidates:^2.0

Standard installation:
TYPO3 Backend / Admin Tools: Extensions / Get Extension: sf_event_mgt_multidates

Note on `ext_emconf.php`: the file lives in the repository, because TER and `typo3/tailor`
need it, but it is marked `export-ignore` in `.gitattributes` and therefore absent from the
Composer package.

2.
Include static TypoScript file 'SF Event Mgt Multidates'

![alt text](https://github.com/machwert/sf_event_mgt_multidates/blob/main/Documentation/TYPO3Backend_IncludeTypoScript.png?raw=true)

## Configuration
In Constant Editor you can configure following:

1. Use FluidEmail to send mails?
plugin.tx_sfeventmgt_mulitdates.settings.useFluidEmail = 1

2. Show registration form on same page in lightbox?
   plugin.tx_sfeventmgt_mulitdates.settings.showFormInLightbox = 1

If you select this (default) the registration form is loaded by ajax in the detail view directly.
The lightbox itself is not implemented in this extension, but it produces a link button
with css class "lightbox-btn" which you can use.

3. TypeNum of ajax page, which has no html header output.
   plugin.tx_sfeventmgt_mulitdates.settings.ajaxTypeNum = 99

4. For this ajax implementation the content of this colPos only is shown.
   plugin.tx_sfeventmgt_mulitdates.settings.ajaxColPos = 20

## TYPO3 Backend sf_event_mgt::Event
Add multiple dates for an event

![alt text](https://github.com/machwert/sf_event_mgt_multidates/blob/main/Documentation/TYPO3Backend_multidates.png?raw=true)

## TYPO3 Frontend - List view
Only the first date ist presented in list view by default

![alt text](https://github.com/machwert/sf_event_mgt_multidates/blob/main/Documentation/TYPO3Frontend_Listview.png?raw=true)

## TYPO3 Frontend - Detail view
All dates are presented in detail view and selectable

![alt text](https://github.com/machwert/sf_event_mgt_multidates/blob/main/Documentation/TYPO3Frontend_Detailview.png?raw=true)

## TYPO3 Frontend - Registration view
All dates are presented in detail view and selectable

![alt text](https://github.com/machwert/sf_event_mgt_multidates/blob/main/Documentation/TYPO3Frontend_Registrationview.png?raw=true)

## TYPO3 Frontend - Calendar view
Events are dislayed multiple times if multiple dates are set in calendar view

![alt text](https://github.com/machwert/sf_event_mgt_multidates/blob/main/Documentation/TYPO3Frontend_Registrationview.png?raw=true)

## TYPO3 Backend - Event Registration view
Registrations you find in TYPO3 Backend for each event in tab 'members (DE: Teilnehmer)'.
Here you find the chosen startdate of the registered user

![alt text](https://github.com/machwert/sf_event_mgt_multidates/blob/main/Documentation/TYPO3Backend_EventRegistrationview.png?raw=true)

## Email New Registration view
In emails to user and admin the chosen date is shown
Attention: For Html-emails as shown there are more adaptions necessary. Possibly I will offer an extension sf_event_mgt_htmlmails shortly

![alt text](https://github.com/machwert/sf_event_mgt_multidates/blob/main/Documentation/Email_NewRegistration.png?raw=true)

## Tests

The extension ships functional tests for the most critical part of the XCLASS: the decision
whether a registration is accepted (`NewRegistrationService::checkRegistrationSuccess()`).

    Tests/Functional/Xclass/NewRegistrationServiceTest.php
    Tests/Functional/Xclass/Fixtures/Registrations.csv

They are *functional* and not unit tests on purpose. The service takes seven constructor
dependencies, `Event::getRegistrations()` resolves the language context through the DI
container, and `emailNotUnique()` issues its own SQL query - none of that can be faked
sensibly without a booted TYPO3 instance.

Note: test and data set names are German, matching the rest of the project's internal notes.

### What is covered

13 cases in total. Seven describe the regular rules:

| Case | Expected result |
|---|---|
| free places available | `REGISTRATION_SUCCESSFUL` |
| unlimited participants (`maxParticipants = 0`) | `REGISTRATION_SUCCESSFUL` |
| registration disabled | `REGISTRATION_NOT_ENABLED` |
| fully booked, no waitlist | `REGISTRATION_FAILED_MAX_PARTICIPANTS` |
| fully booked, waitlist enabled | `REGISTRATION_SUCCESSFUL_WAITLIST` |
| not enough free places for a multi-seat registration | `REGISTRATION_FAILED_NOT_ENOUGH_FREE_PLACES` |
| more seats than `maxRegistrationsPerUser` | `REGISTRATION_FAILED_MAX_AMOUNT_REGISTRATIONS_EXCEEDED` |

Two cover the unique e-mail check and therefore use the CSV fixture: an address already
registered for the event is rejected, a new one is accepted.

**Three pin down the deliberate difference from the original extension.** All of them expect
`REGISTRATION_SUCCESSFUL` where stock `sf_event_mgt` would refuse:

| Case | Original would return |
|---|---|
| event start date in the past | `REGISTRATION_FAILED_EVENT_EXPIRED` |
| registration deadline passed | `REGISTRATION_FAILED_DEADLINE_EXPIRED` |
| end date passed with `allowRegistrationUntilEnddate` | `REGISTRATION_FAILED_EVENT_ENDED` |

The last one is worth knowing about: `REGISTRATION_FAILED_EVENT_ENDED` was introduced in
`sf_event_mgt` after 7.3.3 and was never adopted here. As a consequence the backend checkbox
*"Registration until end date"* has no effect. That is now a test case rather than a silent gap.

### The first test

`derXclassIstUeberhauptAktiv()` asserts that the DI container really returns
`NewRegistrationService`. The XCLASS is registered through
`$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']`, while `EventController` receives the service
via constructor injection - whether the container honours the XCLASS is not obvious from the
code. Without this assertion the remaining twelve cases might be testing the original class
and still pass.

### Running them

The extension ships the tests only; the PHPUnit configuration belongs to the project that runs
them. Use the boilerplate from `typo3/testing-framework`
(`Resources/Core/Build/FunctionalTests.xml` and `FunctionalTestsBootstrap.php`), point its test
suite at `packages/*/Tests/Functional/`, and give the test runner a database user that may
create throwaway databases:

    composer require --dev typo3/testing-framework
    # once, in your database:
    GRANT ALL ON `db\_%`.* TO 'db'@'%';
    vendor/bin/phpunit -c Build/FunctionalTests.xml

The same tests run on both maintained lines - the signature of
`checkRegistrationSuccess()` is identical in sf_event_mgt 8.6 and 9.0.

## ChangeLog

**2.0.2** - Three fixes found by comparing the XCLASSed methods against the current original.
None of them re-introduced a published security advisory - both known advisories for
sf_event_mgt concern the backend module, which this extension does not touch.
- `sendEmailMessage()` now rejects an empty subject and validates `replyTo` with
  `GeneralUtility::validEmail()`, as the original does. `replyTo` can carry user input when
  `notification.registrationDataAsSenderForAdminEmails` is enabled.
- `calendarAction()` registers the page cache tags again, so the calendar page is flushed
  when an event changes.
- `calendarAction()` now updates month **and** year when a week number is given, and uses
  the ISO year `date('o')` - calendar week 1 partly falls into December.
- `initializeAction()` no longer sets `disableOverrideDemand = 0`; it had no effect for the
  detail and registration actions and leaked into other plugins on the same page.

**2.0.1** - Packaging fix, no functional change.
- added `.gitattributes` marking `ext_emconf.php` as `export-ignore`. The file stays in the
  repository for TER and `typo3/tailor`, but is no longer part of the Composer package -
  the same approach `sf_event_mgt` uses.
- `version` in `ext_emconf.php` raised to 2.0.1

**2.0.0** - Support for TYPO3 13.4 with sf_event_mgt ^8.6 and PHP ^8.2.
- `NewEmailService` added as an XCLASS on `EmailService`
- adapted to the sf_event_mgt 8.x API: changed method signatures, the
  `ModifyCheckRegistrationSuccessEvent` and the dropped `$result` parameter
- functional tests added, see [Tests](#tests)
- `ext_emconf.php` kept on this line, but its constraints corrected - they still declared
  TYPO3 12.4, PHP up to 8.2.99 and sf_event_mgt 7.2.0-7.3.3
- fixed version field dropped from `composer.json`; releases are identified by their git tag
- code style aligned with PSR-12

**3.0.0** - Support for TYPO3 14.3, maintained on the `main` branch. That line is
Composer-only and ships no `ext_emconf.php`.

Everything up to here had been developed inside the machwert.de project repository since
early 2024 and was never released separately. These two tags bring that work back.

v0.0.3 - Registration form is now called by Ajax, so event detail page can be cached. Furthermore I changed jQuery implementations to native JavaScript.
v0.0.2 - Moved setting to initializeAction, only calenderAction must be checked now if sf_event_mgt is updated


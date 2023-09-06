# sf_event_mgt_multidates TYPO3 Extension extends sf_event_mgt

Enables multiple dates for sf_event_mgt events
- Adapted calender view
- Can be configured to send FluidEmails
- Can be configured to show registration forms in detail view
- Uses Xclasses (see ext_localconf.php) to extend sf_event_mgt_multidates
- jQuery must be implemented, otherwise registration with multidates does not work
- Attention: For new registrations 'deadline expired' and 'event expired' checks are removed (sf_event_mgt/Classes/Service/RegistrationService.php::checkRegistrationSuccess is Xclassed)
- Most probably need to adjust the shipped fluid templates in sf_event_mgt_multidates/Resources/Private/Extension/sf_event_mgt/.. to your need

## Installation

1.
Composer installation:
composer req machwert/sf_event_mgt_multidates

Standard installation:
TYPO3 Backend / Admin Tools: Extensions / Get Extension: sf_event_mgt_multidates

2.
Include static TypoScript file 'SF Event Mgt Multidates'

![alt text](https://github.com/machwert/sf_event_mgt_multidates/blob/main/Documentation/TYPO3Backend_IncludeTypoScript.png?raw=true)

## Configuration
In Constant Editor you can configure following:

1. Load jQuery 3.7.1 locally?
plugin.tx_sfeventmgt_mulitdates.settings.loadjQuery = 1

2. Use FluidEmail to send mails?
plugin.tx_sfeventmgt_mulitdates.settings.useFluidEmail = 1

3. Content ID of event registration plugin. If set, registration form is displayed in detail view directly.
plugin.tx_sfeventmgt_mulitdates.settings.registrationPluginContentId = 123

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

## ChangeLog
v0.0.2 - Moved setting to initializeAction, only calenderAction must be checked now if sf_event_mgt is updated


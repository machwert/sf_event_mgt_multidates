# sf_event_mgt_multidates TYPO3 Extension extends sf_event_mgt

Enables multiple dates for sf_event_mgt events
- Adapted calender view
- Can be configured to send FluidEmails
- Can be configured to show registration forms in detail view
- Uses Xclasses (see ext_localconf.php) to extend sf_event_mgt_multidates
- Attention: For new registrations 'deadline expired' and 'event expired' checks are removed (sf_event_mgt/Classes/Service/RegistrationService.php::checkRegistrationSuccess is Xclassed)
- Most probably you need to adjust the shipped fluid templates in sf_event_mgt_multidates/Resources/Private/Extension/sf_event_mgt/.. to your need

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

## ChangeLog
v0.0.3 - Registration form is now called by Ajax, so event detail page can be cached. Furthermore I changed jQuery implementations to native JavaScript.
v0.0.2 - Moved setting to initializeAction, only calenderAction must be checked now if sf_event_mgt is updated


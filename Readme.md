# sf_event_mgt_multidates TYPO3 Extension extends sf_event_mgt

- Uses Xclasses (see ext_localconf.php) to extend sf_event_mgt_multidates
- jQuery must be implemented, otherwise registration with multidates does not work
- Attention: For new registrations 'deadline expired' and 'event expired' checks are removed (sf_event_mgt/Classes/Service/RegistrationService.php::checkRegistrationSuccess is Xclassed)

![alt text](https://github.com/machwert/sf_event_mgt_multidates/blob/main/Documentation/TYPO3Backend_multidates.png?raw=true)
![alt text](https://github.com/machwert/sf_event_mgt_multidates/blob/main/Documentation/TYPO3Frontend_Detailview.png?raw=true)
![alt text](https://github.com/machwert/sf_event_mgt_multidates/blob/main/Documentation/TYPO3Frontend_Listview.png?raw=true)
![alt text](https://github.com/machwert/sf_event_mgt_multidates/blob/main/Documentation/TYPO3Frontend_Registrationview.png?raw=true)

## ChangeLog
v0.0.2 - Moved setting to initializeAction, only calenderAction must be checked now if sf_event_mgt is updated

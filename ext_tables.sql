CREATE TABLE tx_sfeventmgt_domain_model_event
(
    startdates int(11) unsigned DEFAULT '0' NOT NULL,
    eventduration int(11) unsigned DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'tx_sfeventmgt_domain_model_registration'
#
CREATE TABLE tx_sfeventmgt_domain_model_registration
(
    startdatetime int(11) DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'tx_sfeventmgtmultidates_domain_model_startdates'
#
CREATE TABLE tx_sfeventmgtmultidates_domain_model_startdates (
    startdatetime int(11) DEFAULT '0' NOT NULL,
	event int(11) unsigned DEFAULT '0' NOT NULL,

	KEY event (event)
);
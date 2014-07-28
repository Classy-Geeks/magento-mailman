<?php


class Classygeeks_Mailman_Helper_Abstract extends Mage_Core_Helper_Abstract
{
	/**
	 * Message Constants
	 */
	// -- Product
	// -- Message Status
	const MAILMAN_MESSAGE_STATUS_QUEUED                             =   1;
	const MAILMAN_MESSAGE_STATUS_ERROR                              =   2;
	const MAILMAN_MESSAGE_STATUS_SENDING                            =   3;
	const MAILMAN_MESSAGE_STATUS_SENT                               =   4;
	// -- Message Body type
	const MAILMAN_MESSAGE_BODYTYPE_TEXT                             =   1;
	const MAILMAN_MESSAGE_BODYTYPE_HTML                             =   2;
	// -- Message Event type
	const MAILMAN_EVENT_QUEUED                                      =   1;
	const MAILMAN_EVENT_SEND_ATTEMPT                                =   2;
	const MAILMAN_EVENT_SENT                                        =   3;
	const MAILMAN_EVENT_REQUEUED                                    =   4;
	const MAILMAN_EVENT_ERROR                                       =   5;
	const MAILMAN_EVENT_PROCESSED                                   =   6;
	const MAILMAN_EVENT_DROPPED                                     =   7;
	const MAILMAN_EVENT_DELIVERED                                   =   8;
	const MAILMAN_EVENT_DEFERRED                                    =   9;
	const MAILMAN_EVENT_BOUNCE                                      =   10;
	const MAILMAN_EVENT_OPEN                                        =   11;
	const MAILMAN_EVENT_CLICK                                       =   12;
	const MAILMAN_EVENT_SPAMREPORT                                  =   13;
	const MAILMAN_EVENT_UNSUBSCRIBE                                 =   14;
	// -- Folders
	const MAILMAN_FOLDER_MAILMAN                                    =   'mailman';
	const MAILMAN_FOLDER_ATTACHMENTS                                =   'attachments';
	const MAILMAN_FOLDER_MESSAGES                                   =   'messages';
}
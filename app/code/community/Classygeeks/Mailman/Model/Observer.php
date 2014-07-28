<?php
/**
 * Copyright 2014 Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Model_Observer extends Mage_Core_Model_Abstract
{

	/**
	 * Send mail
	 */
	public function sendMail()
	{
		// Enabled?
		if (!Mage::getStoreConfig('mailman/settings/enabled')) {
			return false;
		}

		// File lock
		$oIndexLock = new Mage_Index_Model_Process();
		$oIndexLock->setId('mailman_sendmail');

	    try {

		    // Lock
			if ($oIndexLock->isLocked()) {
				throw new Exception('Mail Man Send Mail already running blocked.');
			}
			$oIndexLock->lockAndBlock();

		    // Make sure initialized
		    if (!Mage::helper('mailman')->isInitialized()) {
			    throw new Exception('Mail Man was not initialized successfully.');
		    }

		    // Get all mail messages queued
			$oMessages = Mage::getModel('mailman/messages')->getCollection()->addFieldToFilter('status_id', array('in' => array(Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_STATUS_QUEUED)));
		    $oMessages->setOrder('message_id', 'ASC');
		    $oMessages->setPageSize(intval(Mage::getStoreConfig('mailman/settings/num_sent_cycle')));

		    // Get messages folder
		    $sFolderMessages = Mage::helper('mailman')->getFolder(Classygeeks_Mailman_Helper_Data::MAILMAN_FOLDER_MESSAGES);

		    // Get attachments folder
		    $sFolderAttachments = Mage::helper('mailman')->getFolder(Classygeeks_Mailman_Helper_Data::MAILMAN_FOLDER_ATTACHMENTS);

		    // Make compress filter
		    $oFilterDecompress = new Zend_Filter_Decompress('Gz');

		    // Get transport
		    $oTransport = Mage::helper('mailman')->getTransport();

		    // Each message
		    foreach ($oMessages as $oMessage) {

				// -- Log
		        Mage::log("Mail Man Send: {$oMessage->getMessageId()}", Zend_Log::INFO, 'mailman.log');

			    try {

				    // -- Mark as status sending
				    $oMessage->setStatusId(Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_STATUS_SENDING);
				    $oMessage->save();

				    // -- Event
					$oMessageEvent = Mage::getModel('mailman/events');
				    $oMessageEvent->setMessageId($oMessage->getMessageId());
				    $oMessageEvent->setTypeId(Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_SEND_ATTEMPT);
				    $oMessageEvent->setDateCreated(Mage::app()->getLocale()->date()->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
				    $oMessageEvent->save();

				    // -- Make mail
				    $oMail = new Zend_Mail('utf-8');

				    // -- Message Id
				    $oMail->setMessageId($oMessage->getHash());

				    // -- Set reply to
				    $sReplyTo = $oMessage->getEmailReply();
				    if (!empty($sReplyTo)) {
					    $oMail->setReplyTo($sReplyTo);
				    }
				    unset($sReplyTo);

				    // -- Set return path
				    $sReturnPath = $oMessage->getEmailReturnPath();
				    if (!empty($sReturnPath)) {
					    $oMail->setReturnPath($sReturnPath);
				    }
				    unset($sReturnPath);

				    // -- Set from
				    $aFrom = Zend_Json::decode($oMessage->getEmailFrom());
				    if (empty($aFrom) || !isset($aFrom['email']) || !isset($aFrom['name'])) {
					    throw new Exception('Invalid "From" parameters.');
				    }
				    $oMail->setFrom($aFrom['email'], $aFrom['name']);
				    unset($aFrom);

				    // -- Set to
					$aTo = Zend_Json::decode($oMessage->getEmailTo());
				    if (empty($aTo)) {
					    throw new Exception('Invalid "To" parameters.');
				    }
				    foreach ($aTo as $aThisEmail) {

					    // -- -- Add
					    $oMail->addTo($aThisEmail['email'], $aThisEmail['name']);
				    }
				    unset($aTo);
				    unset($aThisEmail);

				    // -- Set cc
					$aCc = Zend_Json::decode($oMessage->getEmailCc());
					foreach ($aCc as $aThisEmail) {

                        // -- -- Add
					    $oMail->addCc($aThisEmail['email'], $aThisEmail['name']);
				    }
				    unset($aCc);
				    unset($aThisEmail);

				    // -- Set bcc
					$aBcc = Zend_Json::decode($oMessage->getEmailBcc());
					foreach ($aBcc as $aThisEmail) {

                        // -- -- Add
						$oMail->addBcc($aThisEmail['email']);
				    }
				    unset($aBcc);
				    unset($aThisEmail);

				    // -- Set subject
				    $oMail->setSubject($oMessage->getSubject());

				    // -- Set body
				    // -- -- Message file
				    $sMessageFile = "{$sFolderMessages}/{$oMessage->getHash()}";
				    if (!is_file($sMessageFile)) {
					    throw new Exception("Unable to find message file: {$sMessageFile}");
				    }
				    $sBody = $oFilterDecompress->filter(file_get_contents($sMessageFile));
				    if (empty($sBody)) {
					    throw new Exception("Unable read message file: {$sMessageFile}");
				    }
				    // -- -- Set
				    if ($oMessage->getBodyType() == Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_BODYTYPE_TEXT) {
				        $oMail->setBodyText($sBody);
				    }
				    else {
					   $oMail->setBodyHtml($sBody);
				    }
				    unset($sMessageFile);
				    unset($sBody);

				    // -- Set attachments
				    $oAttachments = Mage::getModel('mailman/attachments')->getCollection()->addFieldToFilter('message_id', array('eq' => $oMessage->getMessageId()));
				    foreach ($oAttachments as $oAttachment) {

					    // -- -- -- Attachment file
					    $sAttachmentFile = "{$sFolderAttachments}/{$oAttachment->$sFolderMessages()}";
					    if (!is_file($sAttachmentFile)) {
						    throw new Exception("Unable to find attachment file: {$sAttachmentFile}");
					    }

					    // -- -- -- Decompress
						$sBody = $oFilterDecompress->filter(file_get_contents($sAttachmentFile));
				        if (empty($sBody)) {
						    throw new Exception("Unable read attachment file: {$sAttachmentFile}");
					    }

					    // -- -- -- Add
					    $oMail->createAttachment($sBody, $oAttachment->getFileType(), $oAttachment->getFileDisposition(), $oAttachment->getFileEncoding(), $oAttachment->getFileName());

					    // -- -- -- Cleanup
					    unset($sAttachmentFile);
					    unset($sBody);
				    }

				    // -- Send
				    $oMail->send($oTransport);

				    // -- Event for sent
					$oMessageEvent = Mage::getModel('mailman/events');
				    $oMessageEvent->setMessageId($oMessage->getMessageId());
				    $oMessageEvent->setTypeId(Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_SENT);
				    $oMessageEvent->setDateCreated(Mage::app()->getLocale()->date()->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
				    $oMessageEvent->save();

				    // -- Mark as sent
				    $oMessage->setStatusId(Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_STATUS_SENT);
				    $oMessage->save();

			    }
			    catch (Exception $e) {

				    // -- Event for error
					$oMessageEvent = Mage::getModel('mailman/events');
				    $oMessageEvent->setMessageId($oMessage->getMessageId());
				    $oMessageEvent->setTypeId(Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_ERROR);
				    $oMessageEvent->setDateCreated(Mage::app()->getLocale()->date()->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
				    $oMessageEvent->setParam($e->getMessage());
				    $oMessageEvent->save();

				    // -- Event for requeue
					$oMessageEvent = Mage::getModel('mailman/events');
				    $oMessageEvent->setMessageId($oMessage->getMessageId());
				    $oMessageEvent->setTypeId(Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_REQUEUED);
				    $oMessageEvent->setDateCreated(Mage::app()->getLocale()->date()->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
				    $oMessageEvent->save();

				    // -- Requeue mail?
				    // -- -- Number of attempts
				    $oCoreRead = Mage::getSingleton('core/resource')->getConnection('core_read');
				    $iNumberAttempts = intval($oCoreRead->fetchOne(Mage::getModel('mailman/events')->getCollection()->addFieldToFilter('message_id', array('eq' => $oMessage->getMessageId()))->addFieldToFilter('type_id', array('eq' => Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_SEND_ATTEMPT))->getSelectCountSql()));
					// -- -- Too many?
				    if (intval($iNumberAttempts) >= intval(Mage::getStoreConfig('mailman/settings/num_max_attempts'))) {

					    // -- -- -- Event
					    $oMessageEvent = Mage::getModel('mailman/events');
					    $oMessageEvent->setMessageId($oMessage->getMessageId());
					    $oMessageEvent->setTypeId(Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_ERROR);
					    $oMessageEvent->setDateCreated(Mage::app()->getLocale()->date()->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
					    $oMessageEvent->setParam('Maximum number of attempts exceeded.');
					    $oMessageEvent->save();

					    // -- -- -- Message status error
				        $oMessage->setStatusId(Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_STATUS_ERROR);
					    $oMessage->save();
				    }
				    else {
					    // -- -- -- Requeue
					    $oMessage->setStatusId(Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_STATUS_QUEUED);
					    $oMessage->save();
				    }

					// -- Log
				    Mage::logException($e);
				    Mage::log("Error with Mail Man Send Mail: {$e->getMessage()}", Zend_Log::CRIT, 'mailman.log');

			    }

			    // -- Clean up your mess
				unset($oMail);

		    }

		    // Unlock
			$oIndexLock->unlock();

		    return true;
	    }
	    catch (Exception $e) {

			// Log
		    Mage::logException($e);
		    Mage::log("Error with Mail Man Runcron: {$e->getMessage()}", Zend_Log::CRIT, 'mailman.log');

	    }

		// Unlock
		if ($oIndexLock->isLocked()) {
			$oIndexLock->unlock();
		}

        return false;
	}

	/**
	 * Clean up
	 */
	public function cleanUp()
	{
		// Enabled?
		if (!Mage::getStoreConfig('mailman/settings/enabled')) {
			return false;
		}

		// File lock
		$oIndexLock = new Mage_Index_Model_Process();
		$oIndexLock->setId('mailman_cleanup');

	    try {

		    // Make sure initialized
		    if (!Mage::helper('mailman')->isInitialized()) {
			    throw new Exception('Mail Man was not initialized successfully.');
		    }

		    // Subtract number days to purge
		    $sDatePurge = Mage::app()->getLocale()->date()->sub(intval(Mage::getStoreConfig('mailman/settings/num_days_purge')), Zend_Date::DAY_SHORT)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

		    // Event Id for queued
			$iEventId = Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_QUEUED;

			// Setup collection
		    $oCollection = Mage::getModel('mailman/messages')->getCollection();
		    $sTableEvents = $oCollection->getTable('mailman/events');
			$oCollection->getSelect()->join($sTableEvents, "main_table.message_id = {$sTableEvents}.message_id AND type_id = {$iEventId}", array('date_created'));
		    $oCollection->addFieldToFilter('date_created', array('lteq' => $sDatePurge));

		    // Each old message to delete
		    foreach ($oCollection as $oMessage) {

			    // -- Log
		        Mage::log("Mail Man Clean Up: {$oMessage->getMessageId()}", Zend_Log::INFO, 'mailman.log');

			    // -- Attachments
			    $oAttachments = Mage::getModel('mailman/attachments')->getCollection()->addFieldToFilter('message_id', array('eq' => $oMessage->getMessageId()));
				foreach ($oAttachments as $oAttachment) {

					// -- -- Delete
					$oAttachment->delete();

				}

			    // -- Delete
			    $oMessage->delete();
		    }

	        // Unlock
			$oIndexLock->unlock();

		    return true;
	    }
	    catch (Exception $e) {

			// Log
		    Mage::logException($e);
		    Mage::log("Error with Mail Man Clean Up: {$e->getMessage()}", Zend_Log::CRIT, 'mailman.log');

	    }

		// Unlock
		if ($oIndexLock->isLocked()) {
			$oIndexLock->unlock();
		}
	}

}
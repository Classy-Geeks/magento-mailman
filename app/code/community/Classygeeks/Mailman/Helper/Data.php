<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Helper_Data extends Classygeeks_Mailman_Helper_Abstract
{
	/**
	 * Variables
	 */
	private $_aFolders = null;
	private $_bInitialized = false;

	/**
	 * Public constructor
	 * @return void
	 */
	public function __construct()
	{
		// Create folders
		$sMediaDir = Mage::getBaseDir('var');
		$this->_aFolders = array(
			self::MAILMAN_FOLDER_MAILMAN       =>   "{$sMediaDir}/mailman",
			self::MAILMAN_FOLDER_ATTACHMENTS   =>   "{$sMediaDir}/mailman/attachments",
			self::MAILMAN_FOLDER_MESSAGES      =>   "{$sMediaDir}/mailman/messages",
		);

		// Initialize
		$this->_initialize();
	}

	/**
	 * Initialize
	 */
	private function _initialize()
	{
		// Default to not initialized
		$this->_bInitialized = false;

		try {

			// Initialize folders
			if (!$this->_initializeFolders()) {
				throw new Exception('Unable to initialize folders.');
			}

			// Initialize web hooks
			if (!$this->_initializeWebHooks()) {
				throw new Exception('Unable to initialize webhooks.');
			}

			// Initialized
			$this->_bInitialized = true;
		}
		catch (Exception $e) {

			// Log
		    Mage::logException($e);
		    Mage::log("Error with Mail Man Email Initialization: {$e->getMessage()}", Zend_Log::CRIT, 'mailman.log');

			// Not initialized
			$this->_bInitialized = false;
		}
	}

	/**
	 * Initialize folders
	 * @return boolean
	 */
	private function _initializeFolders()
	{
		// Make folders
		foreach ($this->_aFolders as $sKey => $sFolder) {

			// -- Exist?
			if (!@is_dir($sFolder)) {

				// -- Make
				@mkdir($sFolder);

				// -- Make writable
				@chmod($sFolder, 0777);

				// -- Created?
				if (!@is_dir($sFolder)) {
					return false;
				}

			}
		}

		return true;
	}

	/**
	 * Initialize webhooks
	 * @return boolean
	 */
	private function _initializeWebHooks()
	{
		// Have webhook key?
		$sKey = Mage::getStoreConfig('mailman/webhooks/key');

		// If none, make it
		if (empty($sKey)) {
			Mage::getModel('core/config')->saveConfig('mailman/webhooks/key', $this->makeHash());
		}

		return true;
	}

	/**
	 * Is initialized
	 * @return boolean
	 */
	public function isInitialized()
	{
		return $this->_bInitialized;
	}

	/**
	 * Get transport
	 * @return Zend_Mail_Transport_Smtp
	 */
	public function getTransport()
	{
		// Start with the port
		$aConfig = array(
			'port' => Mage::getStoreConfig('mailman/smtp/port')
		);

		// Ssl?
		if (Mage::getStoreConfig('mailman/smtp/ssl') != 0) {
			$aConfig['ssl'] = ((Mage::getStoreConfig('mailman/smtp/ssl') == 1) ? 'tls' : 'ssl');
		}

		// Authentication
		$sConfigAuth = Mage::getStoreConfig('mailman/smtp/auth');
		if ($sConfigAuth != 'none') {
			$aConfig['auth'] = $sConfigAuth;
			$aConfig['username'] = Mage::getStoreConfig('mailman/smtp/username');
			$aConfig['password'] = Mage::helper('core')->decrypt(Mage::getStoreConfig('mailman/smtp/password'));
		}

		return new Zend_Mail_Transport_Smtp(Mage::getStoreConfig('mailman/smtp/server'), $aConfig);
	}

	/**
	 * Get folder
	 *
	 * @param string Key
	 * @return string
	 */
	public function getFolder($sKey)
	{
		// Return
		return (isset($this->_aFolders[$sKey]) ? $this->_aFolders[$sKey] : false);
	}

	/**
	 * Make has
	 * @param string Param
	 * @return string
	 */
	public function makeHash($sParam = '')
	{
		// Absolutely, 100%, unique
		return hash('sha256', uniqid() . time() . rand(100, 100000000) . $sParam);
	}

	/**
	 * Decode mime part
	 * @param string Content
	 * @param string Encoding
	 * @return string
	 */
	public function decodeMimePart($sContent, $sEncoding)
	{
		// What?
		switch ($sEncoding) {

			// -- Quoted printable
			case Zend_Mime::ENCODING_QUOTEDPRINTABLE:
				return quoted_printable_decode($sContent);

			// -- Base 64
			case Zend_Mime::ENCODING_BASE64:
				return base64_decode($sContent);

			default:
				break;
		}

		return false;
	}

	/**
	 * Convert status id
	 * @param status Id
	 */
	public function convertStatusId($iStatusId)
	{
		// Convert
		switch ($iStatusId) {

			// -- Queued
			case Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_STATUS_QUEUED:
				return $this->__('Queued');

			// -- Queued
			case Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_STATUS_ERROR:
				return $this->__('Error');

			// -- Sent
			case Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_STATUS_SENDING:
				return $this->__('Sending');

			// -- Requeued
			case Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_STATUS_SENT:
				return $this->__('Sent');

			default:
				break;
		}

		return '';
	}

	/**
	 * Convert event id
	 * @param event Id
	 */
	public function convertEventId($iEventId)
	{
		// Convert
		switch ($iEventId) {

			// -- Queued
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_QUEUED:
				return $this->__('Queued');

			// -- Queued
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_SEND_ATTEMPT:
				return $this->__('Send Attempt');

			// -- Sent
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_SENT:
				return $this->__('Sent');

			// -- Requeued
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_REQUEUED:
				return $this->__('Requeued');

			// -- Error
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_ERROR:
				return $this->__('Error');

			// -- Processed
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_PROCESSED:
				return $this->__('Processed');

			// -- Dropped
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_DROPPED:
				return $this->__('Dropped');

			// -- Dropped
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_DROPPED:
				return $this->__('Dropped');

			// -- Delivered
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_DELIVERED:
				return $this->__('Delivered');
				break;

			// -- Deferred
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_DEFERRED:
				return $this->__('Deferred');

			// -- Bounced
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_BOUNCE:
				return $this->__('Bounced');

			// -- Open
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_OPEN:
				return $this->__('Opened');

			// -- Clicked
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_CLICK:
				return $this->__('Clicked');

			// -- Spam Report
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_SPAMREPORT:
				return $this->__('Spam Report');

			// -- Unsubscribed
			case Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_UNSUBSCRIBE:
				return $this->__('Unsubscribed');

			default:
				break;
		}

		return '';
	}

	/**
	 * Format Email addresses
	 * @param string Emails, json encoded
	 * @return string
	 */
	public function formatEmails($sEmails)
	{
		// Decode
		$aEmails = Zend_Json::decode($sEmails);
		if (empty($aEmails)) {
			return '';
		}

		// One array object
		if (isset($aEmails['name'])) {
			$sRet = $aEmails['email'];
			if (!empty($aEmails['name'])) {
				$sRet .= " ({$aEmails['name']})";
			}
			return $sRet;
		}


		// Format
		$aRet = array();
		foreach ($aEmails as $aThisEmail) {
			$sRet = $aThisEmail['email'];
			if (!empty($aThisEmail['name'])) {
				$sRet .= " ({$aThisEmail['name']})";
			}
			$aRet[] = $sRet;
		}

		// Display
		return implode(', ', $aRet);
	}
}


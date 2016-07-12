<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */
class Classygeeks_Mailman_Model_Email_Zendmail extends Zend_Mail
{

	/**
	 * Variables
	 */
	private $_sReplyTo = null;
	private $_sReturnPath = null;
	private $_sToName = null;
	private $_sToEmail = null;
	private $_sFromName = null;
	private $_sFromEmail = null;
	private $_aTo = array();
	private $_aCc = array();
	private $_aBcc = array();
	private $_aAttachments = array();

	/**
	 * Public constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		// Parent
		parent::__construct('utf-8');
	}

	/**
	 * Set reply to
	 *
	 * @param string $sEmail
	 * @return Classygeeks_Mailman_Model_Email
	 */
	public function setReplyTo($sEmail)
	{
		// Set
		$this->_sReplyTo = $sEmail;

		return $this;
	}

	/**
	 * Get reply to
	 *
	 * @return string
	 */
	public function getReplyTo()
	{
		return $this->_sReplyTo;
	}

	/**
	 * Set return path
	 *
	 * @param string $sEmail
	 * @return Classygeeks_Mailman_Model_Email
	 */
	public function setReturnPath($sEmail)
	{
		// Set
		$this->_sReturnPath = $sEmail;

		return $this;
	}

	/**
	 * Get return path
	 *
	 * @return string
	 */
	public function getReturnPath()
	{
		return $this->_sReturnPath;
	}

	/**
	 * Set sender name
	 *
	 * @param string $sName
	 * @return Classygeeks_Mailman_Model_Email
	 */
	public function setSenderName($sName)
	{
		// Set
		$this->setFromName($sName);

		return $this;
	}

	/**
	 * Set sender email
	 *
	 * @param string $sEmail
	 * @return Classygeeks_Mailman_Model_Email
	 */
	public function setSenderEmail($sEmail)
	{
		// Set
		$this->setFromEmail($sEmail);

		return $this;
	}

	/**
	 * Set to name
	 *
	 * @param string $sName
	 * @return Classygeeks_Mailman_Model_Email
	 */
	public function setToName($sName)
	{
		// Set
		$this->_sToName = $sName;

		return $this;
	}

	/**
	 * Get to name
	 *
	 * @return string
	 */
	public function getToName()
	{
		return $this->_sToName;
	}

	/**
	 * Set to email
	 *
	 * @param string $sEmail
	 * @return Classygeeks_Mailman_Model_Email
	 */
	public function setToEmail($sEmail)
	{
		// Set
		$this->_sToEmail = $sEmail;

		return $this;
	}

	/**
	 * Get to email
	 *
	 * @return string
	 */
	public function getToEmail()
	{
		return $this->_sToEmail;
	}

	/**
	 * Set from name
	 *
	 * @param string $sName
	 * @return Classygeeks_Mailman_Model_Email
	 */
	public function setFromName($sName)
	{
		// Set
		$this->_sFromName = $sName;

		return $this;
	}

	/**
	 * Get from name
	 *
	 * @return string
	 */
	public function getFromName()
	{
		return $this->_sFromName;
	}

	/**
	 * Set from email
	 *
	 * @param string $sEmail
	 * @return Classygeeks_Mailman_Model_Email
	 */
	public function setFromEmail($sEmail)
	{
		// Set
		$this->_sFromEmail = $sEmail;

		return $this;
	}

	/**
	 * Get from email
	 *
	 * @return string
	 */
	public function getFromEmail()
	{
		return $this->_sFromEmail;
	}

	/**
	 * Add new "To" recipient to current email
	 *
	 * @param string $sEmail
	 * @param string|null $sName
	 * @return Classygeeks_Mailman_Model_Email
	 */
	public function addTo($sEmail, $sName = null)
	{
		// Make name
		$sName = (empty($sName) ? substr($sEmail, 0, strpos($sEmail, '@')) : $sName);

		// Add
		array_push($this->_aTo, array(
			'email' => $sEmail,
			'name' => $sName
		));

		return $this;
	}

	/**
	 * Get to
	 *
	 * @return array
	 */
	public function getTo()
	{
		return $this->_aTo;
	}

	/**
	 * Add new "Cc" recipient to current email
	 *
	 * @param string $sEmail
	 * @param string|null $sName
	 * @return Classygeeks_Mailman_Model_Email
	 */
	public function addCc($sEmail, $sName = null)
	{
		// Make name
		$sName = (empty($sName) ? substr($sEmail, 0, strpos($sEmail, '@')) : $sName);

		// Add
		array_push($this->_aCc, array(
			'email' => $sEmail,
			'name' => $sName
		));

		return $this;
	}

	/**
	 * Get cc
	 *
	 * @return array
	 */
	public function getCc()
	{
		return $this->_aCc;
	}

	/**
	 * Add new "Bcc" recipient to current email
	 *
	 * @param string $sEmail
	 * @return Classygeeks_Mailman_Model_Email
	 */
	public function addBcc($sEmail)
	{
		// Make name
		$sName = (empty($sName) ? substr($sEmail, 0, strpos($sEmail, '@')) : $sEmail);

		// Add
		array_push($this->_aBcc, array(
			'email' => $sEmail,
			'name' => $sName
		));

		return $this;
	}

	/**
	 * Get bcc
	 *
	 * @return array
	 */
	public function getBcc()
	{
		return $this->_aBcc;
	}

	/**
	 * Adds an existing attachment to the mail message
	 *
	 * @param  Zend_Mime_Part $attachment
	 * @return Zend_Mail Provides fluent interface
	 */
	public function addAttachment(Zend_Mime_Part $oAttachment)
	{
		// Add
		array_push($this->_aAttachments, $oAttachment);

		return $this;
	}

	/**
	 * Creates a Zend_Mime_Part attachment
	 *
	 * Attachment is automatically added to the mail o bject after creation. The
	 * attachment object is returned to allow for further manipulation.
	 *
	 * @param  string $body
	 * @param  string $mimeType
	 * @param  string $disposition
	 * @param  string $encoding
	 * @param  string $filename OPTIONAL A filename for the attachment
	 * @return Zend_Mime_Part Newly created Zend_Mime_Part object (to allow
	 * advanced settings)
	 */
	public function createAttachment($sBody, $sMimeType = Zend_Mime::TYPE_OCTETSTREAM, $sDisposition = Zend_Mime::DISPOSITION_ATTACHMENT, $sEncoding = Zend_Mime::ENCODING_BASE64, $sFilename = null)
	{

		// Create
		$oRet = new Zend_Mime_Part($sBody);
		$oRet->encoding = $sEncoding;
		$oRet->type = $sMimeType;
		$oRet->disposition = $sDisposition;
		$oRet->filename = $sFilename;

		// Add
		$this->addAttachment($oRet);

		return $oRet;
	}

	/**
	 * Get attachments
	 *
	 * @return array
	 */
	public function getAttachments()
	{
		return $this->_aAttachments;
	}
}
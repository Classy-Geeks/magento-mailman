<?php
/**
 * Copyright 2014 Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Model_Email extends Mage_Core_Model_Email
{
	/**
	 * Variables
	 */
	private $_oMail = null;

	/**
	 * Constructor
	 */
	public function __construct()
    {
    }

	/**
     * Retrieve mail object instance
     *
     * @return Classygeeks_Mailman_Model_Email_Zendmail
     */
    public function getMail()
    {
	    // Singleton
        if (empty($this->_oMail)) {
            $this->_oMail = new Classygeeks_Mailman_Model_Email_Zendmail();
        }

        return $this->_oMail;
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
	    $this->getMail()->setReplyTo($sEmail);

        return $this;
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
	    $this->getMail()->setReturnPath($sEmail);

        return $this;
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
	    $this->getMail()->setToName($sName);

        return $this;
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
	    $this->getMail()->setToEmail($sEmail);

        return $this;
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
	    $this->getMail()->setFromName($sName);

        return $this;
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
	    $this->getMail()->setFromEmail($sEmail);

        return $this;
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
	    // Set
	    $this->getMail()->addTo($sEmail, $sName);

        return $this;
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
	    // Set
	    $this->getMail()->addCc($sEmail, $sName);

        return $this;
    }

	/**
     * Add new "Bcc" recipient to current email
     *
     * @param string $sEmail
     * @return Classygeeks_Mailman_Model_Email
     */
    public function addBcc($sEmail)
    {
	    // Set
	    $this->getMail()->addBcc($sEmail);

        return $this;
    }

	/**
	 * Adds an existing attachment to the mail message
	 *
	 * @param  Classygeeks_Mailman_Model_Email_Zendmail_Mailpart $attachment
	 * @return Zend_Mail Provides fluent interface
	 */
	public function addAttachment(Classygeeks_Mailman_Model_Email_Zendmail_Mailpart $oAttachment)
	{
		// Set
	    $this->getMail()->addAttachment($oAttachment);

		return $this;
	}

	/**
	 * Creates a Classygeeks_Mailman_Model_Email_Zendmail_Mailpart attachment
	 *
	 * Attachment is automatically added to the mail o bject after creation. The
	 * attachment object is returned to allow for further manipulation.
	 *
	 * @param  string $body
	 * @param  string $mimeType
	 * @param  string $disposition
	 * @param  string $encoding
	 * @param  string $filename OPTIONAL A filename for the attachment
	 * @return Classygeeks_Mailman_Model_Email_Zendmail_Mailpart Newly created Classygeeks_Mailman_Model_Email_Zendmail_Mailpart object (to allow
	 * advanced settings)
	 */
	public function createAttachment($sBody, $sMimeType = Zend_Mime::TYPE_OCTETSTREAM, $sDisposition = Zend_Mime::DISPOSITION_ATTACHMENT, $sEncoding = Zend_Mime::ENCODING_BASE64, $sFilename = null)
	{
		// Set
	    $this->getMail()->createAttachment($sBody, $sMimeType, $sDisposition, $sEncoding, $sFilename);

		return $this;
	}

	/**
	 * Send
	 * @return Mage_Core_Model_Email
	 */
	public function send()
    {
	    // Enabled
		if (!Mage::getStoreConfig('mailman/settings/enabled')) {
			return parent::send();
		}

	    try {

		    // Make sure initialized
		    if (!Mage::helper('mailman')->isInitialized()) {
			    throw new Exception('Mail Man was not initialized successfully.');
		    }

		    // Log
		    Mage::log("MailMan Email Send: {$this->getSubject()}", Zend_Log::INFO, 'mailman.log');

		    // Make timestamp immediately
		    $sTimestamp = Mage::app()->getLocale()->date()->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

		    // Make compress filter
		    $oFilterCompress = new Zend_Filter_Compress('Gz');

			// Add one "To" to array
		    $sToEmail = $this->getMail()->getToEmail();
		    if (!empty($sToEmail)) {
			    $this->addTo($sToEmail, $this->getMail()->getToName());
		    }

		    // Make a new message
		    $oMessage = Mage::getModel('mailman/messages');

		    // Set status
		    $oMessage->setStatusId(Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_STATUS_QUEUED);

		    // Set return path
		    $sReturnPath = $this->getMail()->getReturnPath();
		    if (!empty($sReturnPath)) {
			    $oMessage->setEmailReturnPath($sReturnPath);
		    }

		    // Set reply
		    $sReplyTo = $this->getMail()->getReplyTo();
		    if (!empty($sReplyTo)) {
			    $oMessage->setEmailReply($sReplyTo);
		    }

		    // Set from
		    $sFromName = $this->getMail()->getFromName();
		    $sFromEmail = $this->getMail()->getFromEmail();
		    $sFromEmail = (empty($sFromEmail) ? Mage::getStoreConfig('mailman/settings/email_default_sender') : $sFromEmail);
		    $aFrom = array(
			    'email' =>  $sFromEmail,
			    'name'  =>  (empty($sFromName) ? substr($sFromEmail, 0, strpos($sFromEmail, '@')) : $sFromName)
		    );
		    $oMessage->setEmailFrom(Zend_Json::encode($aFrom));

		    // Set To
		    $oMessage->setEmailTo(Zend_Json::encode($this->getMail()->getTo()));

		    // Set cc
		    $aCc = $this->getMail()->getCc();
		    if (!empty($aCc)) {
		        $oMessage->setEmailCc(Zend_Json::encode($aCc));
		    }

		    // Set Bcc
		    $aBcc = $this->getMail()->getBcc();
		    if (!empty($aBcc)) {
		        $oMessage->setEmailBcc(Zend_Json::encode($aBcc));
		    }

		    // Set subject
		    $oMessage->setSubject($this->getSubject());

		    // Set body type
		    if (strtolower($this->getType()) == 'html') {
				$oMessage->setBodyType(Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_BODYTYPE_HTML);
		    }
		    else {
			    $oMessage->setBodyType(Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_BODYTYPE_TEXT);
		    }

		    // Write message file
		    // -- Get messages folder
		    $sFolderMessages = Mage::helper('mailman')->getFolder(Classygeeks_Mailman_Helper_Data::MAILMAN_FOLDER_MESSAGES);
		    // -- Compress
			$sBody = $oFilterCompress->filter($this->getBody());
		    // -- Make file hash
		    $oMessage->setHash(Mage::helper('mailman')->makeHash());
		    // -- Write file
		    if (file_put_contents("{$sFolderMessages}/{$oMessage->getHash()}", $sBody) === false) {
			    throw new Exception("Unable to write message to file: {$sFolderMessages}/{$oMessage->getHash()}");
		    }

		    // Save
		    $oMessage->save();

		    // Attachments
		    // -- Get
		    $aAttachments = $this->getMail()->getAttachments();
		    // -- Get attachments folder
			$sFolderAttachments = Mage::helper('mailman')->getFolder(Classygeeks_Mailman_Helper_Data::MAILMAN_FOLDER_ATTACHMENTS);
		    // -- Each attachment
		    foreach ($aAttachments as $oAttachment) {

			    // -- -- Create attachment
			    $oMessageAttachment = Mage::getModel('mailman/attachments');

			    // -- -- Compress
				$sBodyAttachment = $oFilterCompress->filter(Mage::helper('mailman')->decodeMimePart($oAttachment->getContent(), $oAttachment->encoding));

			    // -- -- Set variables
			    $oMessageAttachment->setMessageId($oMessage->getMessageId());
			    $oMessageAttachment->setHash(Mage::helper('mailman')->makeHash());
			    $oMessageAttachment->setFileName($oAttachment->filename);
			    $oMessageAttachment->setFileType($oAttachment->type);
			    $oMessageAttachment->setFileEncoding($oAttachment->encoding);
			    $oMessageAttachment->setFileDisposition($oAttachment->disposition);

			    // -- -- Write attachment to file
			    if (file_put_contents("{$sFolderAttachments}/{$oMessageAttachment->getHash()}", $sBodyAttachment) === false) {

				    // -- -- -- Set status to error for message
		            $oMessage->setStatusId(Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_STATUS_ERROR);
				    $oMessage->save();

				    // -- -- New event
					$oMessageEvent = Mage::getModel('mailman/events');
				    $oMessageEvent->setMessageId($oMessage->getMessageId());
				    $oMessageEvent->setTypeId(Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_ERROR);
				    $oMessageEvent->setParam("Unable to write attachment to file: {$sFolderAttachments}/{$oMessageAttachment->getHash()}");
				    $oMessageEvent->setDateCreated($sTimestamp);
				    $oMessageEvent->save();

				    // -- -- -- Exception
				    throw new Exception("Unable to write attachment to file: {$sFolderAttachments}/{$oMessageAttachment->getHash()}");
			    }

			    // -- Save
			    $oMessageAttachment->save();

			    // -- Clean up your mess
			    unset($oMessageAttachment);
		    }

		    // New event
			$oMessageEvent = Mage::getModel('mailman/events');
		    $oMessageEvent->setMessageId($oMessage->getMessageId());
		    $oMessageEvent->setTypeId(Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_QUEUED);
		    $oMessageEvent->setDateCreated($sTimestamp);
		    $oMessageEvent->save();

		    // Reset
		    unset($this->_oMail);
	    }
	    catch (Exception $e) {

	        // Log
		    Mage::logException($e);
		    Mage::log("Error with Mail Man Email Send: {$e->getMessage()}", Zend_Log::CRIT, 'mailman.log');

		    // Reset
		    unset($this->_oMail);

		    // Attempt parent send
		    return parent::send();
        }

        return $this;
    }
}
<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_MailmanController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Initialize action
	 *
	 * Here, we set the breadcrumbs and the active menu
	 *
	 * @return Mage_Adminhtml_Controller_Action
	 */
	protected function _initAction()
	{
		// Initialize layout, menu, breadcrumbs
		return $this->loadLayout()
			->_setActiveMenu('mailman')
			->_title($this->__('Mail Man / Queue'))
			->_addBreadcrumb($this->__('Mail Man'), $this->__('Mail Man'));
	}

	/**
	 * Queue action
	 */
	public function queueAction()
	{
		// Render layout
		return $this->_initAction()->renderLayout();
	}

	/**
	 * View action
	 */
	public function viewAction()
	{
		// Get Id
		$iMessageId = $this->getRequest()->getParam('id');
		if (empty($iMessageId)) {

			// -- Error
			Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to locate email message.'));

			// -- Go back
			return $this->_redirect('*/*/');
		}

		// Load message
		$oMessage = Mage::getModel('mailman/messages')->load($iMessageId);
		if (empty($oMessage)) {

			// -- Error
			Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to locate email message.'));

			// -- Go back
			return $this->_redirect('*/*/');
		}

		// Set title
		$this->_title($oMessage->getSubject());

		// Set message variables
		Mage::register('message', $oMessage);

		// Get attachments
		$oAttachments = Mage::getModel('mailman/attachments')->getCollection()->addFieldToFilter('message_id', array('eq' => $oMessage->getMessageId()));

		// Set attachments variables
		Mage::register('attachments', $oAttachments);

		// Render layout
		return $this->_initAction()->_addBreadcrumb($this->__('View Message'), $this->__('View Message'))->renderLayout();
	}

	/**
	 * Attachment action
	 */
	public function attachmentAction()
	{
		// Get Id
		$iAttachmentId = $this->getRequest()->getParam('id');
		if (empty($iAttachmentId)) {

			// -- Error
			Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to locate email attachment.'));

			// -- Go back
			return $this->_redirect('*/*/');
		}

		// Load attachment
		$oAttachment = Mage::getModel('mailman/attachments')->load($iAttachmentId);
		if (empty($oAttachment)) {

			// -- Error
			Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to locate email attachment.'));

			// -- Go back
			return $this->_redirect('*/*/');
		}

		// Get attachments folder
		$sFolderAttachments = Mage::helper('mailman')->getFolder(Classygeeks_Mailman_Helper_Data::MAILMAN_FOLDER_ATTACHMENTS);

		// Attachment file
		$sAttachmentFile = "{$sFolderAttachments}/{$oAttachment->getHash()}";
	    if (!is_file($sAttachmentFile)) {

		    // -- Error
			Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to locate email attachment file.'));

			// -- Go back
			return $this->_redirect('*/*/');
	    }

		// Decompress
		$oFilterDecompress = new Zend_Filter_Decompress('Gz');
		$sBody = $oFilterDecompress->filter(file_get_contents($sAttachmentFile));
        if (empty($sBody)) {

	        // -- Error
			Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to read email attachment file.'));

			// -- Go back
			return $this->_redirect('*/*/');
	    }

		// Download
		return $this->_prepareDownloadResponse($oAttachment->getFileName(), $sBody);
	}

	/**
	 * Body action
	 */
	public function bodyAction()
	{
		// Get Id
		$iMessageId = $this->getRequest()->getParam('id');
		if (empty($iMessageId)) {

			// -- Error
			Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to locate email message.'));

			// -- Go back
			return $this->_redirect('*/*/');
		}

		// Load message
		$oMessage = Mage::getModel('mailman/messages')->load($iMessageId);
		if (empty($oMessage)) {

			// -- Error
			Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to locate email message.'));

			// -- Go back
			return $this->_redirect('*/*/');
		}

		// Get messages folder
		$sFolderMessages = Mage::helper('mailman')->getFolder(Classygeeks_Mailman_Helper_Data::MAILMAN_FOLDER_MESSAGES);

		// Attachment file
		$sMessageFile = "{$sFolderMessages}/{$oMessage->getHash()}";
	    if (!is_file($sMessageFile)) {

		    // -- Error
			Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to locate email message file.'));

			// -- Go back
			return $this->_redirect('*/*/');
	    }

		// Decompress
		$oFilterDecompress = new Zend_Filter_Decompress('Gz');
		$sBody = $oFilterDecompress->filter(file_get_contents($sMessageFile));
        if (empty($sBody)) {

	        // -- Error
			Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to read email message file.'));

			// -- Go back
			return $this->_redirect('*/*/');
	    }

		// Content type
		$sContentType = 'text/plain';
		if ($oMessage->getBodyType() == Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_BODYTYPE_HTML) {
			$sContentType = 'text/html';
		}

		// View
		$this->getResponse()->setHeader('Content-Type', $sContentType)->setBody($sBody)->sendResponse();
		exit();
	}

	/**
	 * Requeue action
	 */
	public function requeueAction()
	{
		// Get Id
		$iMessageId = $this->getRequest()->getParam('id');
		if (empty($iMessageId)) {

			// -- Error
			Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to locate email message.'));

			// -- Go back
			return $this->_redirect('*/*/');
		}

		// Load message
		$oMessage = Mage::getModel('mailman/messages')->load($iMessageId);
		if (empty($oMessage)) {

			// -- Error
			Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to locate email message.'));

			// -- Go back
			return $this->_redirect('*/*/');
		}

		// Set status to requeue
		$oMessage->setStatusId(Classygeeks_Mailman_Helper_Data::MAILMAN_MESSAGE_STATUS_QUEUED);
		$oMessage->save();

		// Event
		$oMessageEvent = Mage::getModel('mailman/events');
	    $oMessageEvent->setMessageId($oMessage->getMessageId());
	    $oMessageEvent->setTypeId(Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_REQUEUED);
	    $oMessageEvent->setDateCreated(Mage::app()->getLocale()->date()->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
	    $oMessageEvent->setParam('Requeued by Magento Admin: ' . Mage::getSingleton('admin/session')->getUser()->getName() . ' (' . Mage::getSingleton('admin/session')->getUser()->getUsername() . ')');
	    $oMessageEvent->save();

		// Success
		Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Message requeued for sending.'));

		// Go back
		return $this->_redirectUrl($this->getUrl('*/*/view', array('id' => $oMessage->getMessageId())));
	}

	/**
	 * Test send action
	 */
	public function testSendAction()
	{
		// Assume error
		$bError = true;

		try {

			// Get email
			$sEmail = trim($this->getRequest()->getPost('email'));
			if (strlen($sEmail) == 0 || strpos($sEmail, '@') === false) {
				throw new Exception('Invalid test email.');
			}

			// Get transport
		    $oTransport = Mage::helper('mailman')->getTransport();

			// Make mail
			$oMail = new Zend_Mail('utf-8');

			// Set to
			$oMail->addTo($sEmail);

			// Set subject
			$oMail->setSubject('Mail Man Test Email');

			// Set body type
			$sUrl = Mage::helper('adminhtml')->getUrl();
			$oMail->setBodyText("This is a test email from Mail Man at the location: {$sUrl}");

			// Send
			$oMail->send($oTransport);

			// Success
			$bError = false;

		}
		catch (Exception $e) {

			// Error
			$bError = true;

			// Log
		    Mage::logException($e);
		    Mage::log("Error with Mail Man Test Send: {$e->getMessage()}", Zend_Log::CRIT, 'mailman.log');

		}

		// Send response
		$this->getResponse()->setHeader('Content-Type', 'application/json')->setBody(Zend_Json::encode(array('error' => $bError)))->sendResponse();
		exit();
	}

	/**
	 * Regenerate webhook key
	 */
	public function regenWebHookKeyAction()
	{
		// Assume error
		$bError = true;

		try {

			// Regenerate webhooks
			Mage::getModel('core/config')->saveConfig('mailman/webhooks/key', Mage::helper('mailman')->makeHash());

			// Success
			$bError = false;

		}
		catch (Exception $e) {

			// Error
			$bError = true;

			// Log
		    Mage::logException($e);
		    Mage::log("Error with Mail Man Regenerate Web Hooks: {$e->getMessage()}", Zend_Log::CRIT, 'mailman.log');

		}

		// Send response
		$this->getResponse()->setHeader('Content-Type', 'application/json')->setBody(Zend_Json::encode(array('error' => $bError)))->sendResponse();
		exit();
	}

}
<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_WebhookController extends Mage_Core_Controller_Front_Action
{

	/**
	 * Send grid action
	 */
	public function sendGridAction()
	{
		try {

			// Make sure initialized
		    if (!Mage::helper('mailman')->isInitialized()) {
			    throw new Exception('Mail Man was not initialized successfully.');
		    }

			// Validate key
			$sKey = $this->getRequest()->getParam('api-key');
			if (empty($sKey) || $sKey != Mage::getStoreConfig('mailman/webhooks/key')) {
				throw new Exception('Invalid web hook key.');
			}

			// Get the raw post
			$sPost = file_get_contents('php://input');
			if (empty($sPost)) {
				throw new Exception('Invalid post data.');
			}

			// Decode data
			$oData = Zend_Json::decode($sPost);
			if (empty($oData) || !is_array($oData)) {
				throw new Exception('Invalid json post data.');
			}

			// Each item
			foreach ($oData as $aEvent) {

				// -- Clean smtp Id
				$aEvent['smtp-id'] = ltrim($aEvent['smtp-id'], '<');
				$aEvent['smtp-id'] = rtrim($aEvent['smtp-id'], '>');

				// -- Find message
				$oMessage = Mage::getModel('mailman/messages')->getCollection()->addFieldToFilter('hash', array('eq' => $aEvent['smtp-id']))->getFirstItem();
				if (empty($oMessage)) {
					throw new Exception("Unable to find message hash: {$aEvent['smtp-id']}");
				}

				// -- Convert date
				$aEvent['timestamp'] = Mage::app()->getLocale()->date($aEvent['timestamp'])->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

				// -- Default param
				$sParam = "Email Address: {$aEvent['email']}";

				// -- Find event
				switch (strtolower($aEvent['event'])) {

					// -- -- Processed
					case 'process':
					case 'processed':
						$aEvent['event'] = Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_PROCESSED;
						break;

					// -- -- Dropped
					case 'drop':
					case 'dropped':
						$aEvent['event'] = Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_DROPPED;
						$sParam .= " with reason: {$aEvent['reason']}";
						break;

					// -- -- Delivered
					case 'delivered':
						$aEvent['event'] = Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_DELIVERED;
						$sParam .= " with response: {$aEvent['response']}";
						break;

					// -- -- Deferred
					case 'defer':
					case 'deferred':
						$aEvent['event'] = Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_DEFERRED;
						$sParam .= " with response: {$aEvent['response']}";
						break;

					// -- -- Bounced
					case 'bounce':
					case 'bounced':
						$aEvent['event'] = Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_BOUNCE;
						$sParam .= " with reason: {$aEvent['reason']}";
						break;

					// -- -- Opened
					case 'open':
					case 'opened':
						$aEvent['event'] = Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_OPEN;
						break;

					// -- -- Clicked
					case 'click':
					case 'clicked':
						$aEvent['event'] = Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_CLICK;
						$sParam .= " with url: {$aEvent['url']}";
						break;

					// -- -- Spam Report
					case 'spam report':
						$aEvent['event'] = Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_SPAMREPORT;
						break;

					// -- -- Unsubscribed
					case 'unsubscribe':
					case 'unsubscribed':
						$aEvent['event'] = Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_UNSUBSCRIBE;
						break;

					default:
						break;
				}

				// -- Error?
				if (!is_int($aEvent['event'])) {
					throw new Exception("Invalid Event: {$aEvent['event']}");
				}

				// -- Make event
				$oMessageEvent = Mage::getModel('mailman/events');
			    $oMessageEvent->setMessageId($oMessage->getMessageId());
			    $oMessageEvent->setTypeId($aEvent['event']);
			    $oMessageEvent->setDateCreated($aEvent['timestamp']);
				$oMessageEvent->setParam($sParam);
			    $oMessageEvent->save();

			}

		}
		catch (Exception $e) {

			// Log
		    Mage::logException($e);
		    Mage::log("Error with SendGrid Webhook Callback: {$e->getMessage()}", Zend_Log::CRIT, 'mailman.log');

			// Send 400 response
			return $this->getResponse()->setHeader('HTTP/1.1', '400 Bad Response')->setHeader('Status', '400 Bad Response')->sendResponse();
		}

		// Send 200 response, as not to get flooded
		return $this->getResponse()->setHeader('HTTP/1.1', '200 OK')->setHeader('Status', '200 OK')->sendResponse();
	}
}
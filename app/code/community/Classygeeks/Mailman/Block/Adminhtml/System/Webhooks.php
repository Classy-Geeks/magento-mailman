<?php
/**
 * Copyright 2014 Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Block_Adminhtml_System_Webhooks extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	/**
	 * Construct
	 */
	protected function _construct()
    {
	    // Construct
        parent::_construct();

	    // Set template
        $this->setTemplate('mailman/system/webhooks.phtml');
    }

	/**
     * Return regenerate url
     * @return string
     */
    public function getAjaxRegenerateUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/mailman/regenwebhookkey');
    }

	/**
     * Return sendgrid Url
     * @return string
     */
    public function getSendGridUrl()
    {
        return Mage::getSingleton('core/url')->getUrl('mailman/webhook/sendgrid', array('api-key' => Mage::getStoreConfig('mailman/webhooks/key')));
    }

	/**
	 * Get element html
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $oElement)
    {
	     return $this->_toHtml();
    }

	/**
     * Generate button html
     * @return string
     */
    public function getButtonHtml()
    {
	    // Create button
        $oButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
	            'id'        =>  'mailman_webhookkey',
	            'label'     =>  Mage::helper('mailman')->__('Regenerate Web Hook Key'),
	            'onclick'   =>  'javascript:regenerateKey(); return false;'
        ));

        return $oButton->toHtml();
    }
}
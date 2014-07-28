<?php
/**
 * Copyright 2014 Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */
class Classygeeks_Mailman_Block_Adminhtml_System_Testsend extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	/**
	 * Construct
	 */
	protected function _construct()
    {
	    // Construct
        parent::_construct();

	    // Set template
        $this->setTemplate('mailman/system/testsend.phtml');
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
     * Return url for button
     * @return string
     */
    public function getAjaxUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/mailman/testsend');
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
	            'id'        =>  'mailman_sendtestemail',
	            'label'     =>  Mage::helper('mailman')->__('Send Test Email'),
	            'onclick'   =>  'javascript:sendTestEmail(); return false;'
        ));

        return $oButton->toHtml();
    }
}
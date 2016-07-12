<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Block_Adminhtml_Grid_Queue_Email extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	/**
	 * Render
	 * @param Varien_Object $oRow
	 * @return string
	 */
	public function render(Varien_Object $oRow)
	{
		// Render
		$sEmail = $oRow->getData($this->getColumn()->getIndex());

		// Display
		return Mage::helper('mailman')->formatEmails($sEmail);
	}
}
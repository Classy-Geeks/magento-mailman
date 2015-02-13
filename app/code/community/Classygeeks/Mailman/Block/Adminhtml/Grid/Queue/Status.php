<?php
/**
 * Copyright 2015 Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Block_Adminhtml_Grid_Queue_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	/**
	 * Render
	 * @param Varien_Object $oRow
	 * @return string
	 */
	public function render(Varien_Object $oRow)
	{
		// Render
		$iStatusId = $oRow->getData($this->getColumn()->getIndex());

		// Convert
		return Mage::helper('mailman')->convertStatusId($iStatusId);
	}
}
<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Block_Adminhtml_Grid_Events_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	/**
	 * Render
	 * @param Varien_Object $oRow
	 * @return string
	 */
	public function render(Varien_Object $oRow)
	{
		// Render
		$iTypeId = $oRow->getData($this->getColumn()->getIndex());

		// Convert
		return Mage::helper('mailman')->convertEventId($iTypeId);
	}
}
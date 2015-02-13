<?php
/**
 * Copyright 2015 Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Block_Adminhtml_Grid_Queue extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();

		// Remove add button
		$this->removeButton('add');

		// Set controller
		$this->_controller = 'adminhtml_grid_queue';

		// Block group
		$this->_blockGroup = 'mailman';

		// Header text
		$this->_headerText = $this->__('Queue');

	}

	/**
	 * Get header Css
	 * @return string
	 */
	public function getHeaderCssClass()
	{
        return 'icon-head head-sales-order';
    }
}
<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Model_Config_Adminhtml_Ssl
{
	/**
	 * Option array
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return array(
			array('value' => 0, 'label' => 'None'),
			array('value' => 1, 'label' => 'TLS'),
			array('value' => 2, 'label' => 'SSL')
		);
	}
}
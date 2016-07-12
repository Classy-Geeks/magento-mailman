<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Model_Config_Adminhtml_Auth
{
	/**
	 * Option array
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return array(
			array('value' => 'none', 'label' => 'None'),
			array('value' => 'plain', 'label' => 'Plain'),
			array('value' => 'login', 'label' => 'Login'),
			array('value' => 'crammd5', 'label' => 'CRAM-MD5'),
		);
	}
}
<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Model_Events extends Mage_Core_Model_Abstract
{
	/**
	 * Construction
	 */
	protected function _construct()
    {
	    // Init
        $this->_init('mailman/events');
    }
}
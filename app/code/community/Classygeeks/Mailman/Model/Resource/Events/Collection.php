<?php
/**
 * Copyright 2014 Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Model_Resource_Events_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	/**
     * Class local constructor
     */
    protected function _construct()
    {
	    // Init
		$this->_init('mailman/events');
    }

}
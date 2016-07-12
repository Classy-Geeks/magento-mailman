<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Model_Resource_Messages_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	/**
     * Class local constructor
     */
    protected function _construct()
    {
	    // Init
        $this->_init('mailman/messages');
    }

}
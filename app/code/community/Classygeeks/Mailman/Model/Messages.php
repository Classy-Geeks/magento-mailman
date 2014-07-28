<?php
/**
 * Copyright 2014 Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Model_Messages extends Mage_Core_Model_Abstract
{
	/**
	 * Construction
	 */
	protected function _construct()
    {
	    // Init
        $this->_init('mailman/messages');
    }

	/**
     * Delete object from database
     *
     * @return Mage_Core_Model_Abstract
     */
    public function delete()
    {
	    // Get messages folder
		$sFolderMessages = Mage::helper('mailman')->getFolder(Classygeeks_Mailman_Helper_Data::MAILMAN_FOLDER_MESSAGES);

	    // Message file
	    $sMessageFile = "{$sFolderMessages}/{$this->getHash()}";
	    if (is_file($sMessageFile)) {
		    unlink($sMessageFile);
	    }

	    // Parent
	    parent::delete();
    }
}
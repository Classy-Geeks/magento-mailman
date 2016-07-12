<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */
class Classygeeks_Mailman_Model_Attachments extends Mage_Core_Model_Abstract
{
	/**
	 * Construction
	 */
	protected function _construct()
    {
	    // Init
        $this->_init('mailman/attachments');
    }

	/**
     * Delete object from database
     *
     * @return Mage_Core_Model_Abstract
     */
    public function delete()
    {
	    // Get messages folder
		$sFolderAttachments = Mage::helper('mailman')->getFolder(Classygeeks_Mailman_Helper_Data::MAILMAN_FOLDER_ATTACHMENTS);

	    // Message file
	    $sAttachmentFile = "{$sFolderAttachments}/{$this->getHash()}";
	    if (is_file($sAttachmentFile)) {
		    unlink($sAttachmentFile);
	    }

	    // Parent
	    parent::delete();
    }
}
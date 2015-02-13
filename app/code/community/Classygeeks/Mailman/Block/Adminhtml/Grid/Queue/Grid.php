<?php
/**
 * Copyright 2015 Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Block_Adminhtml_Grid_Queue_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();

		// Default sort
		$this->setDefaultSort('message_id');
		$this->setDefaultDir('DESC');

		// Set Id
		$this->setId('mailman_grid_queue');

		// Save params in session
		$this->setSaveParametersInSession(true);
	}

	/**
	 * Get collection class
	 * @return string
	 */
	protected function _getCollectionClass()
	{
		// This is the model we are using for the grid
		return 'mailman/messages_collection';
	}

	/**
	 * Prepare collection
	 * @return this
	 */
	protected function _prepareCollection()
	{
		// Event Id for queued
		$iEventId = Classygeeks_Mailman_Helper_Data::MAILMAN_EVENT_QUEUED;

		// Get and set our collection for the grid
		$oCollection = Mage::getResourceModel($this->_getCollectionClass());
		$sTableEvents = $oCollection->getTable('mailman/events');
		$oCollection->getSelect()->join($sTableEvents, "main_table.message_id = {$sTableEvents}.message_id AND type_id = {$iEventId}", array('date_created'));

		// Set collection
		$this->setCollection($oCollection);

		// Parent
		parent::_prepareCollection();

		return $this;
	}

	/**
	 * Prepare columns
	 * @return $this
	 */
	protected function _prepareColumns()
	{
		// Add the columns that should appear in the grid
		// -- Message Id
		$this->addColumn('message_id',
			array(
				'header'    => $this->__('Message Id'),
				'align'     => 'center',
				'width'     => '50px',
				'index'     => 'message_id'
			)
		);
		// -- Date
		$this->addColumn('date_created',
			array(
				'header'    => $this->__('Date'),
				'align'     => 'center',
				'width'     => '200px',
				'type'      => 'datetime',
				'index'     => 'date_created'
			)
		);
		// -- Status
		$this->addColumn('status',
			array(
				'header'    => $this->__('Status'),
				'align'     => 'center',
				'width'     => '100px',
				'index'     => 'status_id',
				'renderer'  => 'Classygeeks_Mailman_Block_Adminhtml_Grid_Queue_Status'
			)
		);
		// -- From
		$this->addColumn('from',
			array(
				'header'    => $this->__('From'),
				'index'     => 'email_from',
				'renderer'  => 'Classygeeks_Mailman_Block_Adminhtml_Grid_Queue_Email'
			)
		);
		// -- To
		$this->addColumn('to',
			array(
				'header'    => $this->__('To'),
				'index'     => 'email_to',
				'renderer'  => 'Classygeeks_Mailman_Block_Adminhtml_Grid_Queue_Email'
			)
		);
		// -- Cc
		$this->addColumn('cc',
			array(
				'header'    => $this->__('Cc'),
				'index'     => 'email_cc',
				'renderer'  => 'Classygeeks_Mailman_Block_Adminhtml_Grid_Queue_Email'
			)
		);
		// -- Bcc
		$this->addColumn('bcc',
			array(
				'header'    => $this->__('Bcc'),
				'index'     => 'email_bcc',
				'renderer'  => 'Classygeeks_Mailman_Block_Adminhtml_Grid_Queue_Email'
			)
		);
		// -- Subject
		$this->addColumn('subject',
			array(
				'header'    => $this->__('Subject'),
				'index'     => 'subject'
			)
		);


		return parent::_prepareColumns();
	}

	/**
	 * Get row Url
	 * @param $oRow
	 * @return string
	 */
	public function getRowUrl($oRow)
	{
		// This is where our row data will link to
		return $this->getUrl('*/*/view', array('id' => $oRow->getId()));
	}
}
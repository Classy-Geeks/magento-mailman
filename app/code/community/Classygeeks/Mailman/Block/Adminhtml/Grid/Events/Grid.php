<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

class Classygeeks_Mailman_Block_Adminhtml_Grid_Events_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();

		// Default sort
		$this->setDefaultSort('date_created');
		$this->setDefaultDir('DESC');

		// Set Id
		$this->setId('mailman_grid_events');

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
		return 'mailman/events_collection';
	}

	/**
	 * Prepare collection
	 * @return this
	 */
	protected function _prepareCollection()
	{
		// Get and set our collection for the grid
		$oCollection = Mage::getResourceModel($this->_getCollectionClass())->addFieldToFilter('message_id', intval($this->getRequest()->getParam('id')));

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
		// -- Type
		$this->addColumn('type',
			array(
				'header'    => $this->__('Type'),
				'align'     => 'center',
				'width'     => '100px',
				'index'     => 'type_id',
				'renderer'  => 'Classygeeks_Mailman_Block_Adminhtml_Grid_Events_Type'
			)
		);
		// -- Notes
		$this->addColumn('notes',
			array(
				'header'    => $this->__('Notes'),
				'index'     => 'param'
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
		return null;
	}
}
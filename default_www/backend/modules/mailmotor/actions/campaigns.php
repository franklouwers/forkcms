<?php

/**
 * BackendMailmotorCampaigns
 * This page will display the overview of campaigns
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorCampaigns extends BackendBaseActionIndex
{
	// maximum number of items
	const PAGING_LIMIT = 10;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load datagrid
		$this->loadDataGrid();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Loads the datagrid with the campaigns
	 *
	 * @return	void
	 */
	private function loadDatagrid()
	{
		// create datagrid
		$this->datagrid = new BackendDataGridDB(BackendMailmotorModel::QRY_DATAGRID_BROWSE_CAMPAIGNS);

		// set headers values
		$headers['name'] = ucfirst(BL::getLabel('Title'));
		$headers['created_on'] = ucfirst(BL::getLabel('Created'));

		// set headers
		$this->datagrid->setHeaderLabels($headers);

		// sorting columns
		$this->datagrid->setSortingColumns(array('name', 'created_on'), 'name');
		$this->datagrid->setSortParameter('desc');

		// set column URLs
		$this->datagrid->setColumnURL('name', BackendModel::createURLForAction('index') .'&amp;campaign=[id]');

		// add the multicheckbox column
		$this->datagrid->addColumn('checkbox', '<span class="checkboxHolder"><input type="checkbox" name="toggleChecks" value="toggleChecks" /></span>', '<span><input type="checkbox" name="id[]" value="[id]" class="inputCheckbox" /></span>');
		$this->datagrid->setColumnsSequence('checkbox');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('delete' => BL::getLabel('Delete')), 'delete');
		$this->datagrid->setMassAction($ddmMassAction);

		// set column functions
		$this->datagrid->setColumnFunction(array('BackendDatagridFunctions', 'getTimeAgo'), array('[created_on]'), 'created_on', true);

		// add statistics column
		$this->datagrid->addColumn('statistics');
		$this->datagrid->setColumnAttributes('statistics', array('class' => 'action actionStatistics', 'width' => '10%'));
		$this->datagrid->setColumnFunction(array(__CLASS__, 'setStatisticsLink'), array('[id]'), 'statistics', true);

		// add edit column
		$this->datagrid->addColumn('edit', null, BL::getLabel('Edit'), BackendModel::createURLForAction('edit_campaign') .'&amp;id=[id]', BL::getLabel('Edit'));

		// add styles
		$this->datagrid->setColumnAttributes('name', array('class' => 'title'));

		// set paging limit
		$this->datagrid->setPagingLimit(self::PAGING_LIMIT);
	}


	/**
	 * Parse all datagrids
	 *
	 * @return	void
	 */
	private function parse()
	{
		// parse the datagrid
		$this->tpl->assign('datagrid', ($this->datagrid->getNumResults() != 0) ? $this->datagrid->getContent() : false);
	}


	/**
	 * Sets a link to the campaign statistics if it contains sent mailings
	 *
	 * @return	string
	 * @param	int $id		The ID of the campaign
	 */
	public function setStatisticsLink($id)
	{
		// build the link HTML
		$html = '<a href="'. BackendModel::createURLForAction('statistics_campaign') .'&amp;id='. $id .'" class="button icon iconStats linkButton"><span>'. BL::getLabel('Statistics') .'</span></a>';

		// check if this campaign has sent mailings
		$hasSentMailings = (BackendMailmotorModel::existsSentMailingsByCampaignID($id) > 0) ? true : false;

		// return the result
		return ($hasSentMailings) ? $html : '';
	}
}

?>
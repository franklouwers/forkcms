<?php

/**
 * BackendMailmotorAddresses
 * This page will display the overview of addresses
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorAddresses extends BackendBaseActionIndex
{
	// maximum number of items
	const PAGING_LIMIT = 10;


	/**
	 * Filter variables
	 *
	 * @var	array
	 */
	private $filter;


	/**
	 * The passed group record
	 *
	 * @var	array
	 */
	private $group;


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// set the group
		$this->setGroup();

		// set the filter
		$this->setFilter();

		// load datagrid
		$this->loadDataGrid();

		// load the filter
		$this->loadForm();

		// parse page
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Builds the query for this datagrid
	 *
	 * @return	array		An array with two arguments containing the query and its parameters.
	 */
	private function buildQuery()
	{
		// start query, as you can see this query is built in the wrong place, because of the filter it is a special case
		// where we allow the query to be in the actionfile itself
		$query = 'SELECT ma.email, ma.source, UNIX_TIMESTAMP(ma.created_on) AS created_on
					FROM mailmotor_addresses AS ma
					LEFT OUTER JOIN mailmotor_addresses_groups AS mag ON mag.email = ma.email
					WHERE 1';


		// init parameters
		$parameters = array();

		// add name
		if($this->filter['email'] !== null)
		{
			$query .= ' AND ma.email REGEXP ?';
			$parameters[] = $this->filter['email'];
		}

		// group was set
		if(!empty($this->group))
		{
			$query .= ' AND mag.group_id = ? AND mag.status = ?';
			$parameters[] = $this->group['id'];
			$parameters[] = 'subscribed';
		}

		// group
		$query .= ' GROUP BY email';

		// return
		return array($query, $parameters);
	}


	/**
	 * Loads the datagrid with the e-mail addresses
	 *
	 * @return	void
	 */
	private function loadDatagrid()
	{
		// fetch query and parameters
		list($query, $parameters) = $this->buildQuery();

		// create datagrid
		$this->datagrid = new BackendDataGridDB($query, $parameters);

		// overrule default URL
		$this->datagrid->setURL(BackendModel::createURLForAction(null, null, null, array('offset' => '[offset]', 'order' => '[order]', 'sort' => '[sort]', 'email' => $this->filter['email']), false));

		// add the group to the URL if one is set
		if(!empty($this->group)) $this->datagrid->setURL('&group_id='. $this->group['id'], true);

		// set headers values
		$headers['created_on'] = ucfirst(BL::getLabel('Created'));

		// set headers
		$this->datagrid->setHeaderLabels($headers);

		// sorting columns
		$this->datagrid->setSortingColumns(array('email', 'source', 'created_on'), 'email');

		// add the multicheckbox column
		$this->datagrid->addColumn('checkbox', '<span class="checkboxHolder block"><input type="checkbox" name="toggleChecks" value="toggleChecks" />', '<input type="checkbox" name="emails[]" value="[email]" class="inputCheckbox" /></span>');
		$this->datagrid->setColumnsSequence('checkbox');

		// add mass action dropdown
		$ddmMassAction = new SpoonFormDropdown('action', array('export' => BL::getLabel('Export'), 'delete' => BL::getLabel('Delete')), 'delete');
		$this->datagrid->setMassAction($ddmMassAction);

		// set column functions
		$this->datagrid->setColumnFunction(array('BackendDatagridFunctions', 'getTimeAgo'), array('[created_on]'), 'created_on', true);

		// add edit column
		$editURL = BackendModel::createURLForAction('edit_address') .'&amp;email=[email]';
		if(!empty($this->group)) $editURL .= '&amp;group_id='. $this->group['id'];
		$this->datagrid->addColumn('edit', null, BL::getLabel('Edit'), $editURL, BL::getLabel('Edit'));

		// set paging limit
		$this->datagrid->setPagingLimit(self::PAGING_LIMIT);
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('filter', null, 'get');

		// add fields
		$this->frm->addText('email', $this->filter['email']);
		$this->frm->addHidden('group_id', $this->group['id']);

		// manually parse fields
		$this->frm->parse($this->tpl);

		// check if the filter form was set
		if($this->frm->isSubmitted()) $this->tpl->assign('oPost', true);
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

		// parse paging & sorting
		$this->tpl->assign('offset', (int) $this->datagrid->getOffset());
		$this->tpl->assign('order', (string) $this->datagrid->getOrder());
		$this->tpl->assign('sort', (string) $this->datagrid->getSort());

		// parse filter
		$this->tpl->assign($this->filter);
	}


	/**
	 * Sets the filter based on the $_GET array.
	 *
	 * @return	void
	 */
	private function setFilter()
	{
		// set filter values
		$this->filter['email'] = $this->getParameter('email');
	}


	/**
	 * Sets the group record
	 *
	 * @return	void
	 */
	private function setGroup()
	{
		// set the passed group ID
		$id = SpoonFilter::getGetValue('group_id', null, 0, 'int');

		// group was set
		if(!empty($id))
		{
			// get group record
			$this->group = BackendMailmotorModel::getGroup($id);

			// assign the group record
			$this->tpl->assign('group', $this->group);
		}
	}
}

?>
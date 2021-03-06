<?php

/**
 * BackendMailmotorAjaxLinkAccount
 * This checks if a CampaignMonitor account exists or not, and links it if it does
 *
 * @package		backend
 * @subpackage	mailmotor
 *
 * @author 		Dave Lens <dave@netlash.com>
 * @since		2.0
 */
class BackendMailmotorAjaxLinkAccount extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$url = SpoonFilter::getPostValue('url', null, '');
		$username = SpoonFilter::getPostValue('username', null, '');
		$password = SpoonFilter::getPostValue('password', null, '');

		// check input
		if(empty($url)) $this->output(900, array('field' => 'url'), BL::getError('FieldIsRequired'));
		if(empty($username)) $this->output(900, array('field' => 'username'), BL::getError('FieldIsRequired'));
		if(empty($password)) $this->output(900, array('field' => 'password'), BL::getError('FieldIsRequired'));

		try
		{
			// check if the CampaignMonitor class exists
			if(!SpoonFile::exists(PATH_LIBRARY .'/external/campaignmonitor.php'))
			{
				// the class doesn't exist, so stop here
				$this->output(self::BAD_REQUEST, null, BL::getError('ClassDoesNotExist', 'mailmotor'));
			}

			// require CampaignMonitor class
			require_once 'external/campaignmonitor.php';

			// init CampaignMonitor object
			$cm = new CampaignMonitor($url, $username, $password, 5);

			// get the client gettings from the install
			$companyName = BackendModel::getModuleSetting('mailmotor', 'cm_client_company_name');
			$contactEmail = BackendModel::getModuleSetting('mailmotor', 'cm_client_contact_email');
			$contactName = BackendModel::getModuleSetting('mailmotor', 'cm_client_contact_name');
			$country = BackendModel::getModuleSetting('mailmotor', 'cm_client_country');
			$timezone = BackendModel::getModuleSetting('mailmotor', 'cm_client_timezone');

			// create a client
			try
			{
				$clientID = $cm->createClient($companyName, $contactName, $contactEmail, $country, $timezone);
			}
			catch(Exception $e) {}

			// save the new data
			BackendModel::setModuleSetting('mailmotor', 'cm_url', $url);
			BackendModel::setModuleSetting('mailmotor', 'cm_username', $username);
			BackendModel::setModuleSetting('mailmotor', 'cm_password', $password);

			// account was linked
			BackendModel::setModuleSetting('mailmotor', 'cm_account', true);

			// client ID was set
			if(!empty($clientID)) BackendModel::setModuleSetting('mailmotor', 'cm_client_id', $clientID);
		}

		catch(Exception $e)
		{
			// timeout occured
			if($e->getMessage() == 'Error Fetching http headers') $this->output(self::BAD_REQUEST, null, BL::getError('CmTimeout', 'mailmotor'));

			// other error
			$this->output(900, array('field' => 'url'), sprintf(BL::getError('CampaignMonitorError', 'mailmotor'), $e->getMessage()));
		}

		// CM was successfully initialized
		$this->output(self::OK, array('client_id' => (!empty($clientID) ? $clientID : null), 'message' => 'account-linked'), BL::getMessage('AccountLinked', 'mailmotor'));
	}
}

?>
<?php

/**
 * BackendAnalyticsConfig
 * This is the configuration-object for the analytics module
 *
 * @package		backend
 * @subpackage	analytics
 *
 * @author 		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
final class BackendAnalyticsConfig extends BackendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var	string
	 */
	protected $defaultAction = 'index';


	/**
	 * The disabled actions
	 *
	 * @var	array
	 */
	protected $disabledActions = array();


	/**
	 * Check if all required settings have been set
	 *
	 * @return	void
	 */
	public function __construct($module)
	{
		// parent construct
		parent::__construct($module);

		// init
		$error = false;

		// analytics session token
		if(BackendModel::getModuleSetting('analytics', 'session_token') === null) $error = true;

		// analytics table id
		if(BackendModel::getModuleSetting('analytics', 'table_id') === null) $error = true;

		// missing settings, so redirect to the settings-page
		if($error && Spoon::isObjectReference('url') && Spoon::getObjectReference('url')->getAction() != 'settings') SpoonHTTP::redirect(BackendModel::createURLForAction('settings'));
	}
}

?>
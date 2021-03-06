<?php

/**
 * ModuleInstaller
 * The base-class for the installer
 *
 * @package		installer
 * @subpackage	core
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @author 		Matthias Mullie <matthias@netlash.com>
 * @since		2.0
 */
class ModuleInstaller
{
	/**
	 * Database connection instance
	 *
	 * @var SpoonDatabase
	 */
	private $db;


	/**
	 * The active languages
	 *
	 * @var	array
	 */
	private $languages = array();


	/**
	 * The variables passed by the installer
	 *
	 * @var	array
	 */
	private $variables = array();


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	SpoonDatabase $db	The database-connection.
	 * @param	array $languages	The selected languages
	 * @param	bool $example		Should example data be installed
	 * @param	array $variables	The passed variables
	 */
	public function __construct(SpoonDatabase $db, array $languages, $example = false, array $variables = array())
	{
		// set DB
		$this->db = $db;
		$this->languages = $languages;
		$this->example = (bool) $example;
		$this->variables = $variables;

		// call the execute method
		$this->execute();
	}


	/**
	 * Inserts a new module
	 *
	 * @return	void
	 * @param	string $name					The name of the module
	 * @param	string[optional] $description	A description for the module
	 */
	protected function addModule($name, $description = null)
	{
		// redefine
		$name = (string) $name;

		// module does not yet exists
		if(!(bool) $this->getDB()->getVar('SELECT COUNT(name) FROM modules WHERE name = ?;', $name))
		{
			// build item
			$item = array('name' => $name,
							'description' => $description,
							'active' => 'Y');

			// insert module
			$this->getDB()->insert('modules', $item);
		}

		// activate and update description
		else $this->getDB()->update('modules', array('description' => $description, 'active' => 'Y'), 'name = ?', $name);
	}


	/**
	 * Method that will be overriden by the specific installers
	 *
	 * @return void
	 */
	protected function execute()
	{
		// just a placeholder
	}


	/**
	 * Get the database-handle
	 *
	 * @return	SpoonDatabase
	 */
	protected function getDB()
	{
		return $this->db;
	}


	/**
	 * Get the default user
	 *
	 * @return	int
	 */
	protected function getDefaultUserID()
	{
		try
		{
			// fetch default user id
			return (int) $this->getDB()->getVar('SELECT id
													FROM users
													WHERE is_god = ? AND active = ? AND deleted = ?
													ORDER BY id ASC',
													array('Y', 'Y', 'N'));
		}

		// catch exceptions
		catch(Exception $e)
		{
			return 1;
		}
	}


	/**
	 * Get the selected languages
	 *
	 * @return	void
	 */
	protected function getLanguages()
	{
		return $this->languages;
	}


	/**
	 * Get a setting
	 *
	 * @return	mixed
	 * @param	string $module	The name of the module.
	 * @param	string $name	The name of the setting.
	 */
	protected function getSetting($module, $name)
	{
		return unserialize($this->getDB()->getVar('SELECT value
													FROM modules_settings
													WHERE module = ? AND name = ?',
													array((string) $module, (string) $name)));
	}


	/**
	 * Get a variable
	 *
	 * @return	mixed
	 * @param	string $name		The name of the variable
	 */
	protected function getVariable($name)
	{
		// is the variable available?
		if(!isset($this->variables[$name])) return null;

		// return the real value
		return $this->variables[$name];
	}


	/**
	 * Imports the sql file
	 *
	 * @return	void
	 * @param	string $filename	The full path for the SQL-file
	 */
	protected function importSQL($filename)
	{
		// load the file content and execute it
		$content = trim(SpoonFile::getContent($filename));

		// file actually has content
		if(!empty($content))
		{
			/**
			 * Some versions of PHP can't handle multiple statements at once, so split them
			 * We know this isn't the best solution, but we couldn't find a beter way.
			 * @later: find a beter way to handle multiple-line queries
			 */
			$queries = explode(";\n", $content);

			// loop queries and execute them
			foreach($queries as $query) $this->getDB()->execute($query);
		}
	}


	/**
	 * Insert an extra
	 *
	 * @return	int
	 * @param	string $module
	 * @param	string $type
	 * @param	string $label
	 * @param	string $action
	 * @param	string[optional] $data
	 * @param	bool[optional] $hidden
	 * @param	int[optional] $sequence
	 */
	protected function insertExtra($module, $type, $label, $action = null, $data = null, $hidden = false, $sequence = null)
	{
		// no sequence set
		if(is_null($sequence))
		{
			// set next sequence number for this module
			$sequence = $this->getDB()->getVar('SELECT MAX(sequence) + 1 FROM pages_extras WHERE module = ?', array((string) $module));

			// this is the first extra for this module: generate new 1000-series
			if(is_null($sequence)) $sequence = $sequence = $this->getDB()->getVar('SELECT CEILING(MAX(sequence) / 1000) * 1000 FROM pages_extras');
		}

		// redefine
		$module = (string) $module;
		$type = (string) $type;
		$label = (string) $label;
		$action = !is_null($action) ? (string) $action : null;
		$data = !is_null($data) ? (string) $data : null;
		$hidden = $hidden && $hidden !== 'N' ? 'Y' : 'N';
		$sequence = (int) $sequence;

		// build item
		$item = array('module' => $module,
						'type' => $type,
						'label' => $label,
						'action' => $action,
						'data' => $data,
						'hidden' => $hidden,
						'sequence' => $sequence);

		// doesn't already exist
		if($this->getDB()->getVar('SELECT COUNT(id) FROM pages_extras WHERE module = ? AND type = ? AND label = ?', array($item['module'], $item['type'], $item['label'])) == 0)
		{
			// insert extra and return id
			return (int) $this->getDB()->insert('pages_extras', $item);
		}

		// return id
		else return (int) $this->getDB()->getVar('SELECT id FROM pages_extras WHERE module = ? AND type = ? AND label = ?', array($item['module'], $item['type'], $item['label']));
	}


	/**
	 * Inserts a new locale item
	 *
	 * @return	void
	 * @param	string $language
	 * @param	string $application
	 * @param	string $module
	 * @param	string $type
	 * @param	string $name
	 * @param	string $value
	 */
	protected function insertLocale($language, $application, $module, $type, $name, $value)
	{
		// redefine
		$language = (string) $language;
		$application = SpoonFilter::getValue($application, array('frontend', 'backend'), '');
		$module = (string) $module;
		$type = SpoonFilter::getValue($type, array('act', 'err', 'lbl', 'msg'), '');
		$name = (string) $name;
		$value = (string) $value;

		// validate
		if($application == '') throw new Exception('Invalid application. Possible values are: backend, frontend.');
		if($type == '') throw new Exception('Invalid type. Possible values are: act, err, lbl, msg.');

		// check if the label already exists
		if(!(bool) $this->getDB()->getVar('SELECT COUNT(i.id)
											FROM locale AS i
											WHERE i.language = ? AND i.application = ? AND i.module = ? AND i.type = ? AND i.name = ?',
											array($language, $application, $module, $type, $name)))
		{
			// insert
			$this->db->insert('locale', array('user_id' => $this->getDefaultUserID(),
												'language' => $language,
												'application' => $application,
												'module' => $module,
												'type' => $type,
												'name' => $name,
												'value' => $value,
												'edited_on' => gmdate('Y-m-d H:i:s')));
		}
	}


	/**
	 * Insert a meta item
	 *
	 * @return	int
	 * @param	string $keywords
	 * @param	string $description
	 * @param	string $title
	 * @param	string $url
	 * @param	bool[optional] $keywordsOverwrite
	 * @param	bool[optional] $descriptionOverwrite
	 * @param	bool[optional] $titleOverwrite
	 * @param	bool[optional] $urlOverwrite
	 * @param	string[optional] $custom
	 */
	protected function insertMeta($keywords, $description, $title, $url, $keywordsOverwrite = false, $descriptionOverwrite = false, $titleOverwrite = false, $urlOverwrite = false, $custom = null)
	{
		// redefine
		$keywords = (string) $keywords;
		$keywordsOverwrite = $keywordsOverwrite && $keywordsOverwrite !== 'N' ? 'Y' : 'N';
		$description = (string) $description;
		$descriptionOverwrite = $titleOverwrite && $titleOverwrite !== 'N' ? 'Y' : 'N';
		$title = (string) $title;
		$titleOverwrite = $titleOverwrite && $titleOverwrite !== 'N' ? 'Y' : 'N';
		$url = (string) $url;
		$urlOverwrite = $urlOverwrite && $urlOverwrite !== 'N' ? 'Y' : 'N';
		$custom = !is_null($custom) ? (string) $custom : null;

		// build item
		$item = array('keywords' => $keywords,
						'keywords_overwrite' => $keywordsOverwrite,
						'description' => $description,
						'description_overwrite' => $descriptionOverwrite,
						'title' => $title,
						'title_overwrite' => $titleOverwrite,
						'url' => $url,
						'url_overwrite' => $urlOverwrite,
						'custom' => $custom);

		// insert meta and return id
		return (int) $this->getDB()->insert('meta', $item);
	}


	/**
	 * Insert a page
	 *
	 * @return	void
	 * @param	array $revision
	 * @param	array[optional] $meta
	 * @param	array[optional] $block
	 */
	protected function insertPage(array $revision, array $meta = null, array $block = null)
	{
		// redefine
		$revision = (array) $revision;
		$meta = (array) $meta;

		// build revision
		if(!isset($revision['language'])) throw new SpoonException('language is required for installing pages');
		if(!isset($revision['title'])) throw new SpoonException('title is required for installing pages');
		if(!isset($revision['id'])) $revision['id'] = (int) $this->getDB()->getVar('SELECT MAX(id) + 1 FROM pages WHERE language = ?', array($revision['language']));
		if(!$revision['id']) $revision['id'] = 1;
		if(!isset($revision['user_id'])) $revision['user_id'] = $this->getDefaultUserID();
		if(!isset($revision['template_id'])) $revision['template_id'] = 1;
		if(!isset($revision['type'])) $revision['type'] = 'page';
		if(!isset($revision['parent_id'])) $revision['parent_id'] = ($revision['type'] == 'page' ? 1 : 0);
		if(!isset($revision['navigation_title'])) $revision['navigation_title'] = $revision['title'];
		if(!isset($revision['navigation_title_overwrite'])) $revision['navigation_title_overwrite'] = 'N';
		if(!isset($revision['hidden'])) $revision['hidden'] = 'N';
		if(!isset($revision['status'])) $revision['status'] = 'active';
		if(!isset($revision['publish_on'])) $revision['publish_on'] = gmdate('Y-m-d H:i:s');
		if(!isset($revision['created_on'])) $revision['created_on'] = gmdate('Y-m-d H:i:s');
		if(!isset($revision['edited_on'])) $revision['edited_on'] = gmdate('Y-m-d H:i:s');
		if(!isset($revision['data'])) $revision['data'] = null;
		if(!isset($revision['allow_move'])) $revision['allow_move'] = 'Y';
		if(!isset($revision['allow_children'])) $revision['allow_children'] = 'Y';
		if(!isset($revision['allow_edit'])) $revision['allow_edit'] = 'Y';
		if(!isset($revision['allow_delete'])) $revision['allow_delete'] = 'Y';
		if(!isset($revision['no_follow'])) $revision['no_follow'] = 'N';
		if(!isset($revision['sequence'])) $revision['sequence'] = (int) $this->getDB()->getVar('SELECT MAX(sequence) + 1 FROM pages WHERE language = ? AND parent_id = ? AND type = ?', array($revision['language'], $revision['parent_id'], $revision['type']));
		if(!isset($revision['extra_ids'])) $revision['extra_ids'] = null;
		if(!isset($revision['has_extra'])) $revision['has_extra'] = $revision['extra_ids'] ? 'Y' : 'N';

		// meta needs to be inserted
		if(!isset($revision['meta_id']))
		{
			// build meta
			if(!isset($meta['keywords'])) $meta['keywords'] = $revision['title'];
			if(!isset($meta['keywords_overwrite'])) $meta['keywords_overwrite'] = false;
			if(!isset($meta['description'])) $meta['description'] = $revision['title'];
			if(!isset($meta['description_overwrite'])) $meta['description_overwrite'] = false;
			if(!isset($meta['title'])) $meta['title'] = $revision['title'];
			if(!isset($meta['title_overwrite'])) $meta['title_overwrite'] = false;
			if(!isset($meta['url'])) $meta['url'] = SpoonFilter::urlise($revision['title']);
			if(!isset($meta['url_overwrite'])) $meta['url_overwrite'] = false;
			if(!isset($meta['custom'])) $meta['custom'] = null;

			// insert meta
			$revision['meta_id'] = $this->insertMeta($meta['keywords'], $meta['description'], $meta['title'], $meta['url'], $meta['keywords_overwrite'], $meta['description_overwrite'], $meta['title_overwrite'], $meta['url_overwrite'], $meta['custom']);
		}

		// insert page
		$revision['revision_id'] = $this->getDB()->insert('pages', $revision);

		// get number of blocks to insert
		$numBlocks = $this->getDB()->getVar('SELECT MAX(num_blocks) FROM pages_templates WHERE active = ?', array('Y'));

		// get arguments (this function has a variable length argument list, to allow multiple blocks to be added)
		$blocks = array();

		// loop blocks
		for($i = 0; $i < $numBlocks; $i++)
		{
			// get block
			$block = @func_get_arg($i + 2);
			if($block === false) $block = array();
			else $block = (array) $block;

			// build block
			if(!isset($block['id'])) $block['id'] = $i;
			if(!isset($block['revision_id'])) $block['revision_id'] = $revision['revision_id'];
			if(!isset($block['status'])) $block['status'] = 'active';
			if(!isset($block['created_on'])) $block['created_on'] = gmdate('Y-m-d H:i:s');
			if(!isset($block['edited_on'])) $block['edited_on'] = gmdate('Y-m-d H:i:s');
			if(!isset($block['extra_id'])) $block['extra_id'] = null;
			else $revision['extra_ids'] = trim($revision['extra_ids'] .','. $block['extra_id'], ',');
			if(!isset($block['html'])) $block['html'] = '';
			elseif(SpoonFile::exists($block['html'])) $block['html'] = SpoonFile::getContent($block['html']);

			// insert block
			$this->getDB()->insert('pages_blocks', $block);
		}

		// blocks added
		if($revision['extra_ids'] && $revision['has_extra'] == 'N')
		{
			// update page
			$revision['has_extra'] = 'Y';
			$this->getDB()->update('pages', $revision, 'revision_id = ?', array($revision['revision_id']));
		}

		// return page id
		return $revision['id'];
	}


	/**
	 * Should example data be installed
	 *
	 * @return	bool
	 */
	protected function installExample()
	{
		return $this->example;
	}


	/**
	 * Make a module searchable
	 *
	 * @return	void
	 * @param	string $module						The module to make searchable.
	 * @param	bool[optional] $searchable			Enable/disable search for this module by default?
	 * @param	int[optional] $weight				Set default search weight for this module.
	 */
	protected function makeSearchable($module, $searchable = true, $weight = 1)
	{
		// redefine
		$module = (string) $module;
		$searchable = $searchable && $searchable !== 'N' ? 'Y' : 'N';
		$weight = (int) $weight;

		// make module searchable
		$this->getDB()->execute('INSERT INTO search_modules (module, searchable, weight) VALUES (?, ?, ?)
									ON DUPLICATE KEY UPDATE searchable = ?, weight = ?', array($module, $searchable, $weight, $searchable, $weight));
	}


	/**
	 * Set the rights for an action
	 *
	 * @return	void
	 * @param	int $groupId			The group wherefor the rights will be set.
	 * @param	string $module			The module wherin the action appears.
	 * @param	string $action			The action wherefor the rights have to set.
	 * @param	int[optional] $level	The leve, default is 7 (max).
	 */
	protected function setActionRights($groupId, $module, $action, $level = 7)
	{
		// redefine
		$groupId = (int) $groupId;
		$module = (string) $module;
		$action = (string) $action;
		$level = (int) $level;

		// action doesn't exist
		if(!(bool) $this->getDB()->getVar('SELECT COUNT(id)
											FROM groups_rights_actions
											WHERE group_id = ? AND module = ? AND action = ?',
											array($groupId, $module, $action)))
		{
			// build item
			$item = array('group_id' => $groupId,
							'module' => $module,
							'action' => $action,
							'level' => $level);

			// insert
			$this->getDB()->insert('groups_rights_actions', $item);
		}
	}


	/**
	 * Sets the rights for a module
	 *
	 * @return	void
	 * @param	int $groupId		The group wherefor the rights will be set.
	 * @param	string $module		The module too set the rights for.
	 */
	protected function setModuleRights($groupId, $module)
	{
		// redefine
		$groupId = (int) $groupId;
		$module = (string) $module;

		// module doesn't exist
		if(!(bool) $this->getDB()->getVar('SELECT COUNT(id)
											FROM groups_rights_modules
											WHERE group_id = ? AND module = ?',
											array((int) $groupId, (string) $module)))
		{
			// build item
			$item = array('group_id' => $groupId,
							'module' => $module);

			// insert
			$this->getDB()->insert('groups_rights_modules', $item);
		}
	}


	/**
	 * Stores a module specific setting in the database.
	 *
	 * @return	void
	 * @param	string $module				The module wherefore the setting will be set.
	 * @param	string $name				The name of the setting.
	 * @param	mixed[optional] $value		The optional value.
	 * @param	bool[optional] $overwrite	Overwrite no matter what.
	 */
	protected function setSetting($module, $name, $value = null, $overwrite = false)
	{
		// redefine
		$module = (string) $module;
		$name = (string) $name;
		$value = serialize($value);
		$overwrite = (bool) $overwrite;

		// doens't already exist
		if(!(bool) $this->getDB()->getVar('SELECT COUNT(name)
											FROM modules_settings
											WHERE module = ? AND name = ?;',
											array($module, $name)))
		{
			// build item
			$item = array('module' => $module,
							'name' => $name,
							'value' => $value);

			// insert setting
			$this->getDB()->insert('modules_settings', $item);
		}

		// overwrite
		elseif($overwrite)
		{
			// insert setting
			$this->getDB()->execute('INSERT INTO modules_settings (module, name, value) VALUES (?, ?, ?)
										ON DUPLICATE KEY UPDATE value = ?', array($module, $name, $value, $value));
		}
	}
}


/**
 * CoreInstall
 * Installer for the core
 *
 * @package		installer
 * @subpackage	core
 *
 * @author		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class CoreInstall extends ModuleInstaller
{
	/**
	 * Installe the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// validate variables
		if($this->getVariable('default_language') === null) throw new SpoonException('Default language is not provided.');
		if($this->getVariable('site_domain') === null) throw new SpoonException('Site domain is not provided.');
		if($this->getVariable('spoon_debug_email') === null) throw new SpoonException('Spoon debug email is not provided.');
		if($this->getVariable('api_email') === null) throw new SpoonException('API email is not provided.');
		if($this->getVariable('site_title') === null) throw new SpoonException('Site title is not provided.');

		// import SQL
		$this->importSQL(PATH_WWW .'/backend/core/installer/install.sql');

		// add core modules
		$this->addModule('core', 'The Fork CMS core module.');
		$this->addModule('authentication', 'The module to manage authentication');
		$this->addModule('dashboard', 'The dashboard containing module specific widgets.');
		$this->addModule('error', 'The error module, used for displaying errors.');

		// set rights
		$this->setRights();

		// set settings
		$this->setSettings();
	}


	/**
	 * Set the rights
	 *
	 * @return	void
	 */
	private function setRights()
	{
		// module rights
		$this->setModuleRights(1, 'dashboard');

		// action rights
		$this->setActionRights(1, 'dashboard', 'index');
	}


	/**
	 * Store the settings
	 *
	 * @return	void
	 */
	private function setSettings()
	{
		// languages settings
		$this->setSetting('core', 'languages', $this->getLanguages(), true);
		$this->setSetting('core', 'active_languages', $this->getLanguages(), true);
		$this->setSetting('core', 'redirect_languages', $this->getLanguages(), true);
		$this->setSetting('core', 'default_language', $this->getVariable('default_language'), true);
		$this->setSetting('core', 'interface_languages', array('nl', 'en'), true);
		$this->setSetting('core', 'default_interface_language', 'en', true);

		// other settings
		$this->setSetting('core', 'theme');
		$this->setSetting('core', 'akismet_key', '');
		$this->setSetting('core', 'google_maps_keky', '');
		$this->setSetting('core', 'max_num_revisions', 20);
		$this->setSetting('core', 'site_domains', array($this->getVariable('site_domain')));
		$this->setSetting('core', 'site_html_header', '');
		$this->setSetting('core', 'site_html_footer', '');

		// date & time
		$this->setSetting('core', 'date_format_short', 'j.n.Y');
		$this->setSetting('core', 'date_formats_short', array('j/n/Y', 'j-n-Y', 'j.n.Y', 'n/j/Y', 'n/j/Y', 'n/j/Y', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'm/d/Y', 'm-d-Y', 'm.d.Y', 'j/n/y', 'j-n-y', 'j.n.y', 'n/j/y', 'n-j-y', 'n.j.y', 'd/m/y', 'd-m-y', 'd.m.y', 'm/d/y', 'm-d-y', 'm.d.y'));
		$this->setSetting('core', 'date_format_long', 'l j F Y');
		$this->setSetting('core', 'date_formats_long', array('j F Y', 'D j F Y', 'l j F Y', 'j F, Y', 'D j F, Y', 'l j F, Y', 'd F Y', 'd F, Y', 'F j Y', 'D F j Y', 'l F j Y', 'F d, Y', 'D F d, Y', 'l F d, Y'));
		$this->setSetting('core', 'time_format', 'H:i');
		$this->setSetting('core', 'time_formats', array('H:i', 'H:i:s', 'g:i a', 'g:i A'));

		// e-mail settings
		$this->setSetting('core', 'mailer_from', array('name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email')));
		$this->setSetting('core', 'mailer_to', array('name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email')));
		$this->setSetting('core', 'mailer_reply_to', array('name' => 'Fork CMS', 'email' => $this->getVariable('spoon_debug_email')));

		// stmp settings
		$this->setSetting('core', 'smtp_server', $this->getVariable('smtp_server'));
		$this->setSetting('core', 'smtp_port', $this->getVariable('smtp_port'));
		$this->setSetting('core', 'smtp_username', $this->getVariable('smtp_username'));
		$this->setSetting('core', 'smtp_password', $this->getVariable('smtp_password'));

		// default titles
		$siteTitles = array('nl' => 'Mijn website', 'fr' => 'Mon site web', 'en' => 'My website');

		// language specific
		foreach($this->getLanguages() as $language)
		{
			// set title
			$this->setSetting('core', 'site_title_'. $language, (isset($siteTitles[$language])) ? $siteTitles[$language] : $this->getVariable('site_title'));
		}

		/*
		 * We're going to try to install the settings for the api.
		 */
		require_once PATH_LIBRARY .'/external/fork_api.php';

		// create new instance
		$api = new ForkAPI();

		try
		{
			// get the keys
			$keys = $api->coreRequestKeys($this->getVariable('site_domain'), $this->getVariable('api_email'));

			// ap settings
			$this->setSetting('core', 'fork_api_public_key', $keys['public']);
			$this->setSetting('core', 'fork_api_private_key', $keys['private']);

			// set keys
			$api->setPublicKey($keys['public']);
			$api->setPrivateKey($keys['private']);

			// get services
			$services = (array) $api->pingGetServices();

			// set services
			if(!empty($services)) $this->setSetting('core', 'ping_services', array('services' => $services, 'date' => time()));
		}

		// catch exceptions
		catch(Exception $e)
		{
			// we don't need those keys.
		}
	}
}

?>
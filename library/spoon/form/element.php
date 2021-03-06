<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */


/**
 * The base class for every form element.
 *
 * @package		spoon
 * @subpackage	form
 *
 *
 * @author		Davy Hellemans <davy@spoon-library.com>
 * @since		0.1.1
 */
class SpoonFormElement
{
	/**
	 * Custom attributes for this element
	 *
	 * @var	array
	 */
	protected $attributes = array();


	/**
	 * Name of the form this element is a part of
	 *
	 * @var	string
	 */
	protected $formName;


	/**
	 * Method inherited from the form (post/get)
	 *
	 * @var	string
	 */
	protected $method = 'post';


	/**
	 * Reserved attributes. Can not be overwritten using setAttribute(s)
	 *
	 * @var	array
	 */
	protected $reservedAttributes = array('type', 'name', 'value');


	/**
	 * Retrieve the form method or the submitted data.
	 *
	 * @return	string
	 * @param	bool[optional] $array
	 */
	public function getMethod($array = false)
	{
		// we want to get the actual $_GET or $_POST data
		if($array)
		{
			// $_POST array
			if($this->method == 'post') return $_POST;

			// $_GET array
			return SpoonFilter::arrayMapRecursive('urldecode', $_GET);
		}

		// submitted via met get or post
		return $this->method;
	}


	/**
	 * Retrieve the unique name of this object.
	 *
	 * @return	string
	 */
	public function getName()
	{
		return $this->attributes['name'];
	}


	/**
	 * Returns whether this form has been submitted.
	 *
	 * @return	bool
	 */
	public function isSubmitted()
	{
		// post/get data
		$data = $this->getMethod(true);

		// name given
		if($this->formName != null && isset($data['form']) && $data['form'] == $this->formName) return true;

		// no name given
		elseif($this->formName == null && $_SERVER['REQUEST_METHOD'] == strtoupper($this->method)) return true;

		// everything else
		return false;
	}


	/**
	 * Parse the html for the current element.
	 *
	 * @return	void
	 * @param	SpoonTemplate[optional] $template
	 */
	public function parse(SpoonTemplate $template = null)
	{
		// filled by subclasses
	}


	/**
	 * Set the name of the form this field is a part of.
	 *
	 * @return	void
	 * @param	string $name
	 */
	public function setFormName($name)
	{
		$this->formName = (string) $name;
	}


	/**
	 * Set the form method.
	 *
	 * @return	void
	 * @param	string[optional] $method
	 */
	public function setMethod($method = 'post')
	{
		$this->method = SpoonFilter::getValue($method, array('get', 'post'), 'post');
	}
}

?>
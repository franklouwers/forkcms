<?php

/**
 * BackendForm
 * this is our extended version of SpoonForm
 *
 * @package		backend
 * @subpackage	core
 *
 * @author 		Davy Hellemans <davy@netlash.com>
 * @author 		Tijs Verkoyen <tijs@netlash.com>
 * @since		2.0
 */
class BackendForm extends SpoonForm
{
	/**
	 * The header instance
	 *
	 * @var	BackendHeader
	 */
	private $header;


	/**
	 * The URL instance
	 *
	 * @var	BackendURL
	 */
	private $URL;


	/**
	 * Show the global error
	 *
	 * @var	bool
	 */
	private $useGlobalError = true;


	/**
	 * Default constructor
	 *
	 * @return	void
	 * @param	string[optional] $name			Name of the form.
	 * @param	string[optional] $action		The action (URL) whereto the form will be submitted, if not provided it will be autogenerated.
	 * @param	string[optional] $method		The method to use when submiting the form, default is POST.
	 * @param	bool[optional] $useToken		Should we automagically add a formtoken?
	 * @param	bool[optional] $useGlobalError	Should we automagically show a global error?
	 */
	public function __construct($name = null, $action = null, $method = 'post', $useToken = true, $useGlobalError = true)
	{
		// init the URL-instance
		if(Spoon::isObjectReference('url')) $this->URL = Spoon::getObjectReference('url');
		if(Spoon::isObjectReference('header')) $this->header = Spoon::getObjectReference('header');
		$this->useGlobalError = (bool) $useGlobalError;

		// build a name if there wasn't one provided
		$name = ($name === null) ? SpoonFilter::toCamelCase($this->URL->getModule() .'_'. $this->URL->getAction(), '_', true) : (string) $name;

		// build the action if it wasn't provided
		$action = ($action === null) ? '/'. str_replace(array('&', '&&amp;'), '&amp;', $this->URL->getQueryString()) : (string) $action;

		// call the real form-class
		parent::__construct($name, $action, $method, $useToken);

		// add default classes
		$this->setParameter('id', $name);
		$this->setParameter('class', 'forkForms submitWithLink');
	}


	/**
	 * Adds a button to the form
	 *
	 * @return	SpoonButton
	 * @param	string $name				Name of the button.
	 * @param	string $value				The value (or label) that will be printed.
	 * @param	string[optional] $type		The type of the button (submit is default).
	 * @param	string[optional] $class		Class(es) that will be applied on the button.
	 */
	public function addButton($name, $value, $type = 'submit', $class = null)
	{
		// redefine
		$name = (string) $name;
		$value = (string) $value;
		$type = (string) $type;
		$class = ($class !== null) ? (string) $class : 'inputButton';

		// do a check
		if($type == 'submit' && $name == 'submit') throw new BackendException('You can\'t add buttons with the name submit. JS freaks out when we replace the buttons with a link and use that link to submit the form.');

		// call the real form class
		return parent::addButton($name, $value, $type, $class);
	}


	/**
	 * Adds a single checkbox.
	 *
	 * @return	void
	 * @param	string $name					The name of the element.
	 * @param	bool[optional] $checked			Should the checkbox be checked?
	 * @param	string[optional] $class			Class(es) that will be applied on the element.
	 * @param	string[optional] $classError	Class(es) that will be applied on the element when an error occurs.
	 */
	public function addCheckbox($name, $checked = false, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$checked = (bool) $checked;
		$class = ($class !== null) ? (string) $class : 'inputCheckbox';
		$classError = ($classError !== null) ? (string) $classError : 'inputCheckboxError';

		// return element
		return parent::addCheckbox($name, $checked, $class, $classError);
	}


	/**
	 * Adds a datefield to the form
	 *
	 * @return	SpoonDateField
	 * @param	string $name					Name of the element
	 * @param	int[optional] $value			The value for the element
	 * @param	string[optional] $type			The type (from, till, range) of the datepicker
	 * @param	int[optional] $date				The date to use.
	 * @param	int[optional] $date2			The second date for a rangepicker.
	 * @param	string[optional] $class			Class(es) that have to be applied on the element.
	 * @param	string[optional] $classError	Class(es) that have to be applied when an error occurs on the element.
	 */
	public function addDate($name, $value = null, $type = null, $date = null, $date2 = null, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$value = ($value !== null) ? (int) $value : null;
		$type = SpoonFilter::getValue($type, array('from', 'till', 'range'), 'none');
		$date = ($date !== null) ? (int) $date : null;
		$date2 = ($date2 !== null) ? (int) $date2 : null;
		$class = ($class !== null) ? (string) $class : 'inputText inputDate';
		$classError = ($classError !== null) ? (string) $classError : 'inputTextError inputDateError';

		// validate
		if($type == 'from' && ($date == 0 || $date == null)) throw new BackendException('A datefield with type "from" should have a valid date-parameter.');
		if($type == 'till' && ($date == 0 || $date == null)) throw new BackendException('A datefield with type "till" should have a valid date-parameter.');
		if($type == 'range' && ($date == 0 || $date2 == 0 || $date == null || $date2 == null)) throw new BackendException('A datefield with type "range" should have 2 valid date-parameters.');

		// @later	get prefered mask & first day
		$mask = 'd/m/Y';
		$firstday = 1;

		// rebuild mask
		$relMask = str_replace(array('d', 'm', 'Y', 'j', 'n'), array('dd', 'mm', 'yy', 'd', 'm'), $mask);

		// build rel
		$rel = $relMask .':::'. $firstday;

		// add extra classes based on type
		switch($type)
		{
			case 'from':
				$class .= ' inputDatefieldFrom inputText';
				$classError .= ' inputDatefieldFrom';
				$rel .= ':::'. date('Y-m-d', $date);
			break;

			case 'till':
				$class .= ' inputDatefieldTill inputText';
				$classError .= ' inputDatefieldTill';
				$rel .= ':::'. date('Y-m-d', $date);
			break;

			case 'range':
				$class .= ' inputDatefieldRange inputText';
				$classError .= ' inputDatefieldRange';
				$rel .= ':::'. date('Y-m-d', $date) .':::'. date('Y-m-d', $date2);
			break;

			default:
				$class .= ' inputDatefieldNormal inputText';
				$classError .= ' inputDatefieldNormal';
			break;
		}

		// call parent
		parent::addDate($name, $value, $mask, $class, $classError);

		// set attributes
		parent::getField($name)->setAttributes(array('rel' => $rel));

		// fetch field
		return parent::getField($name);
	}


	/**
	 * Adds a single dropdown.
	 *
	 * @return	void
	 * @param	string $name						Name of the element.
	 * @param	array $values						Values for the dropdown
	 * @param	string[optional] $selected			The selected elements.
	 * @param	bool[optional] $multipleSelection	Is it possible to select multiple items?
	 * @param	string[optional] $class				Class(es) that will be applied on the element.
	 * @param	string[optional] $classError		Class(es) that will be applied on the element when an error occurs.
	 */
	public function addDropdown($name, array $values = null, $selected = null, $multipleSelection = false, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$values = (array) $values;
		$selected = ($selected !== null) ? $selected : null;
		$multipleSelection = (bool) $multipleSelection;
		$class = ($class !== null) ? (string) $class : 'select';
		$classError = ($classError !== null) ? (string) $classError : 'selectError';

		// special classes for multiple
		if($multipleSelection)
		{
			$class .= ' selectMultiple';
			$classError .= ' selectMultipleError';
		}

		// return element
		return parent::addDropdown($name, $values, $selected, $multipleSelection, $class, $classError);
	}


	/**
	 * Add an editor field
	 *
	 * @return	void
	 * @param	string $name					The name of the element
	 * @param	string[optional] $value			The value inside the element
	 * @param	string[optional] $class			Class(es) that will be applied on the element.
	 * @param	string[optional] $classError	Class(es) that will be applied on the element when an error occurs.
	 * @param	bool[optional] $HTML			Will the field contain HTML?
	 */
	public function addEditor($name, $value = null, $class = null, $classError = null, $HTML = true)
	{
		// redefine
		$name = (string) $name;
		$value = ($value !== null) ? (string) $value : null;
		$class = 'inputEditor '. (string) $class;
		$classError = 'inputEditorError '. (string) $classError;
		$HTML = (bool) $HTML;

		// we add JS because we need TinyMCE
		$this->header->addJavascript('tiny_mce/tiny_mce.js', 'core');
		$this->header->addJavascript('tiny_mce/tiny_mce_config.js', 'core', true);

		// add the field
		return $this->addTextArea($name, $value, $class, $classError, $HTML);
	}


	/**
	 * Adds a single file field.
	 *
	 * @return	void
	 * @param	string $name					Name of the element.
	 * @param	string[optional] $class			Class(es) that will be applied on the element.
	 * @param	string[optional] $classError	Class(es) that will be applied on the element when an error occurs.
	 */
	public function addFile($name, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$class = ($class !== null) ? (string) $class : 'inputFile';
		$classError = ($classError !== null) ? (string) $classError : 'inputFileError';

		// return element
		return parent::addFile($name, $class, $classError);
	}


	/**
	 * Adds a single image field.
	 *
	 * @return	void
	 * @param	string $name					The name of the element.
	 * @param	string[optional] $class			Class(es) that will be applied on the element.
	 * @param	string[optional] $classError	Class(es) that will be applied on the element when an error occurs.
	 */
	public function addImage($name, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$class = ($class !== null) ? (string) $class : 'inputFile inputImage';
		$classError = ($classError !== null) ? (string) $classError : 'inputFileError inputImageError';

		// return element
		return parent::addImage($name, $class, $classError);
	}


	/**
	 * Adds a multiple checkbox.
	 *
	 * @return	void
	 * @param	string $name					The name of the element.
	 * @param	array $values					The values for the checkboxes.
	 * @param	mixed[optional] $checked		Should the checkboxes be checked?
	 * @param	string[optional] $class			Class(es) that will be applied on the element.
	 * @param	string[optional] $classError	Class(es) that will be applied on the element when an error occurs.
	 */
	public function addMultiCheckbox($name, array $values, $checked = null, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$values = (array) $values;
		$checked = ($checked !== null) ? (array) $checked : null;
		$class = ($class !== null) ? (string) $class : 'inputCheckbox';
		$classError = ($classError !== null) ? (string) $classError : 'inputCheckboxError';

		// return element
		return parent::addMultiCheckbox($name, $values, $checked, $class, $classError);
	}


	/**
	 * Adds a single password field.
	 *
	 * @return	void
	 * @param	string $name					The name of the field.
	 * @param	string[optional] $value			The value for the field.
	 * @param	int[optional] $maxlength		The maximum length for the field.
	 * @param	string[optional] $class			Class(es) that will be applied on the element.
	 * @param	string[optional] $classError	Class(es) that will be applied on the element when an error occurs.
	 * @param	bool[optional] $HTML			Will the field contain HTML?
	 */
	public function addPassword($name, $value = null, $maxlength = null, $class = null, $classError = null, $HTML = false)
	{
		// redefine
		$name = (string) $name;
		$value = ($value !== null) ? (string) $value : null;
		$maxlength = ($maxlength !== null) ? (int) $maxlength : null;
		$class = ($class !== null) ? (string) $class : 'inputText inputPassword';
		$classError = ($classError !== null) ? (string) $classError : 'inputTextError inputPasswordError';
		$HTML = (bool) $HTML;

		// return element
		return parent::addPassword($name, $value, $maxlength, $class, $classError, $HTML);
	}


	/**
	 * Adds a single radiobutton.
	 *
	 * @return	void
	 * @param	string $name					The name of the element
	 * @param	array $values					The possible values for the radiobutton
	 * @param	string[optional] $checked		Should the element be checked?
	 * @param	string[optional] $class			Class(es) that will be applied on the element.
	 * @param	string[optional] $classError	Class(es) that will be applied on the element when an error occurs.
	 */
	public function addRadiobutton($name, array $values, $checked = null, $class = null, $classError = null)
	{
		// redefine
		$name = (string) $name;
		$values = (array) $values;
		$checked = ($checked !== null) ? (string) $checked : null;
		$class = ($class !== null) ? (string) $class : 'inputRadio';
		$classError = ($classError !== null) ? (string) $classError : 'inputRadioError';

		// return element
		return parent::addRadiobutton($name, $values, $checked, $class, $classError);
	}


	/**
	 * Adds a single textarea.
	 *
	 * @return	void
	 * @param	string $name					The name of the element.
	 * @param	string[optional] $value			The value inside the element.
	 * @param	string[optional] $class			Class(es) that will be applied on the element.
	 * @param	string[optional] $classError	Class(es) that will be applied on the element when an error occurs.
	 * @param	bool[optional] $HTML			Will the element contain HTML?
	 */
	public function addTextarea($name, $value = null, $class = null, $classError = null, $HTML = false)
	{
		// redefine
		$name = (string) $name;
		$value = ($value !== null) ? (string) $value : null;
		$class = ($class !== null) ? (string) $class : 'textarea';
		$classError = ($classError !== null) ? (string) $classError : 'textareaError';
		$HTML = (bool) $HTML;

		// return element
		return parent::addTextarea($name, $value, $class, $classError, $HTML);
	}


	/**
	 * Adds a single textfield.
	 *
	 * @return	void
	 * @param	string $name					The name of the element.
	 * @param	string[optional] $value			The value inside the element.
	 * @param	int[optional] $maxlength		The maximum length for the value.
	 * @param	string[optional] $class			Class(es) that will be applied on the element.
	 * @param	string[optional] $classError	Class(es) that will be applied on the element when an error occurs.
	 * @param	bool[optional] $HTML			Will this element contain HTML?
	 */
	public function addText($name, $value = null, $maxlength = 255, $class = null, $classError = null, $HTML = false)
	{
		// redefine
		$name = (string) $name;
		$value = ($value !== null) ? (string) $value : null;
		$maxlength = ($maxlength !== null) ? (int) $maxlength : null;
		$class = ($class !== null) ? (string) $class : 'inputText';
		$classError = ($classError !== null) ? (string) $classError : 'inputTextError';
		$HTML = (bool) $HTML;

		// return element
		return parent::addText($name, $value, $maxlength, $class, $classError, $HTML);
	}


	/**
	 * Adds a single timefield.
	 *
	 * @return	void
	 * @param	string $name					The name of the element.
	 * @param	string[optional] $value			The value inside the element.
	 * @param	string[optional] $class			Class(es) that will be applied on the element.
	 * @param	string[optional] $classError	Class(es) that will be applied on the element when an error occurs.
	 */
	public function addTime($name, $value = null, $class = null, $classError = null)
	{
		$name = (string) $name;
		$value = ($value !== null) ? (string) $value : null;
		$class = ($class !== null) ? (string) $class : 'inputText inputTime';
		$classError = ($classError !== null) ? (string) $classError : 'inputTextError inputTimeError';

		// return element
		return parent::addTime($name, $value, $class, $classError);
	}


	/**
	 * Fetches all the values for this form as key/value pairs
	 *
	 * @return	array
	 * @param	mixed[optional] $excluded		Which elements should be excluded?
	 */
	public function getValues($excluded = array('form', 'save', 'form_token'))
	{
		return parent::getValues($excluded);
	}


	/**
	 * Parse the form
	 *
	 * @return	void
	 * @param	SpoonTemplate $tpl	The template instance wherein the form will be parsed.
	 */
	public function parse(SpoonTemplate $tpl)
	{
		// parse the form
		parent::parse($tpl);

		// validate the form
		$this->validate();

		// if the form is submitted but there was an error, assign a general error
		if($this->useGlobalError && $this->isSubmitted() && !$this->isCorrect()) $tpl->assign('formError', true);
	}
}

?>
<?php
/**
 * Base Validate Class
 *
 * Used to validate individual fields in a form
 *
 * @author Nick DeNardis <nick.denardis@gmail.com>
 */
class Validate {
	/**
	* @var array 
	*/
	private $types = array();

	/**
	 * Validate Constructor
	 * 
	 * Used to setup all the validation types
	 *
	 * @return bool
	 */
	public function __construct(){
		$this->types = array('email' => '^[a-zA-Z0-9_\.-]+@[a-zA-Z0-9\.-]+\.[a-zA-Z]{2,4}$',
				'phone' => '^[0-9]{3}-[0-9]{3}-[0-9]{4}$',
				'int' => '^[0-9-]*$',
				'unsigned' => '^[0-9]*$',
				'alpha' => '^[a-zA-Z]$',
				'alphanum' => '^[a-zA-Z0-9]$',
				'float' => '^[0-9]*\\.?[0-9]*$',
				'url' => '^[a-zA-Z0-9]+://[^ ]+$');

		return true;
	}

	/**
	 * Validate a Field
	 *
	 * @param $type String of the Validation Type
	 * @param $value Mixed value that needs to be validated
	 * @return bool
	 */
	public function Check($type, $value){
		// Check for the type
		if ((string)$value != '' && array_key_exists($type, $this->types))
			return ereg($this->types[$type], $value);
		
		return true;
	}

	/**
	 * Add Validation Type
	 *
	 * @param $type String of the Validation Type
	 * @param $regex String of the Regular Expression
	 * @return bool
	 */
	public function AddValidation($type, $regex){
		// Add the Type to the list
		$this->types[$type] = $regex;

		return true;
	}
}
?>

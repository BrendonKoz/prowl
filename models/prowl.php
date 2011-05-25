<?php
class Prowl extends ProwlAppModel {
	var $useDbConfig = 'prowl';
	var $name = 'Prowl';
	var $useTable = false;
	var $_schema = array(
		'apikey' => array(
			'type' => 'string',
			'null' => false,
			'length' => 204,
		),
		'providerkey' => array(
			'type' => 'string',
			'null' => true,
			'length' => 40
		),
		'priority' => array(
			'type' => 'integer',
			'null' => true,
			'length' => 11
		),
		'application' => array(
			'type' => 'string',
			'null' => false,
			'length' => 256
		),
		'event' => array(
			'type' => 'string',
			'null' => true,
			'length' => 1024
		),
		'description' => array(
			'type' => 'string',
			'null' => true,
			'length' => 10000
		)
	);
	var $validate = array(
		'apikey' => array(
			'alphaNumericList' => array(
				'rule' => '/^((?:[a-z0-9]{40},?)){1,5}$/',
				'required' => true,
				'message' => 'A valid API key is required.'
			),
			'minLength' => array(
				'rule' => array('minLength', 40),
				'message' => 'An API key contains 40 characters.'
			),
			'maxLength' => array(
				'rule' => array('maxLength', 204),
				'message' => 'An API key contains 40 characters, no more than 5 keys may be listed at a time.'
			),
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field must not be left blank.'
			)
		),
		'providerkey' => array(
			'alphaNumeric' => array(
				'rule' => 'alphaNumeric',
				'message' => 'A valid API key is required.'
			),
			'between' => array(
				'rule' => array('between', 40, 40),
				'message' => 'An API key contains 40 characters.'
			)
		),
		'priority' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'required' => true,
				'message' => 'This field is required and can only contain a number.'
			),
			'inList' => array(
				'rule' => array('inList', array(-2, -1, 0, 1, 2)),
				'message' => 'This field\'s value must be set between -2 and 2.'
			)
		),
		'application' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => 'This field cannot be left blank.'
			),
			'between' => array(
				'rule' => array('between', 1, 255),
				'message' => 'You must provide an application name between 1 and 255 characters.'
			)
		),
		'event' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 1024),
				'message' => 'An event name can only contain 1024 characters.'
			),
			'compareFieldsForValue' => array(
				'rule' => array('oneOfTwoNotEmpty', 'description'),
				'message' => 'Either the "event" field or the "description" field must have a value.'
			)
		),
		'description' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 10000),
				'message' => 'An event name can only contain 1024 characters.'
			),
			'compareFieldsForValue' => array(
				'rule' => array('oneOfTwoNotEmpty', 'event'),
				'message' => 'Either the "event" field or the "description" field must have a value.'
			)
		)
	);

	function __construct() {
		App::import(array('type' => 'File', 'name' => 'Prowl.PROWL_CONFIG', 'file' => 'config'.DS.'prowl.php'));
		App::import(array('type' => 'File', 'name' => 'Prowl.ProwlSource', 'file' => 'models'.DS.'datasources'.DS.'prowl_source.php'));
		$config =& new PROWL_CONFIG();
		ConnectionManager::create('prowl', $config->prowl);

		parent::__construct();
	}

	function schema(){
		return array();
	}

	function oneOfTwoNotEmpty($fieldValue, $fieldNameToCompare){
		if(!empty($fieldValue)){
			return true;
		}else{
			if(!empty($this->data[$this->name][$fieldNameToCompare])){
				return true;
			}
		}
		return false;
	}
}
?>
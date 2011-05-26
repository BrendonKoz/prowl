<?php
/**
 * Prowl DataSource
 * Used for writing to Prowl, through models.
 * PHP Version 5.x
 */
class ProwlSource extends DataSource {
	var $apikey      = '';
	var $description = 'Prowl API';
	var $endpoint    = 'https://api.prowlapp.com/publicapi/';
	var $socket      = null;
	var $response    = null;

	public function __construct($config){
		if(isset($config['login']) || isset($config['apikey'])){
			//provider's api key is optional unless whitelisted 
			$this->apikey = isset($config['login']) ? $config['username'] : $config['apikey'];
		}

		parent::__construct($config);

		App::import('Core', 'HttpSocket');
		$this->socket = new HttpSocket();
	}

	public function listSources() {
		return false;
	}

	public function create($model, $fields = array(), $values = array()) {
		$data = array_combine($fields, $values);
		$data = array_merge(array('providerkey' => $this->apikey), $data);	//merge provider key
		$result = $this->socket->post($this->endpoint.'add', $data);
		$this->response = $result = $this->_convert($result);
		if(isset($result['Prowl']['Success'])){
			return true;
		}
		return false;
	}

	public function verify($data){
		//$data in form of array('apikey'=>40 byte string, ['providerkey'=>optional 40 byte string])
		$data = array_merge(array('providerkey' => $this->apikey), $data);	//merge provider key
		$result = $this->socket->get($this->endpoint.'verify', $data);
		$this->response = $result = $this->_convert($result);
		if(isset($result['Prowl']['Success'])){
			return true;
		}
		return false;
	}

	public function _convert($data){
		if($data === false){
			return array(
				'Prowl' => array(
					'error' => array(
						'value' => 'Internal server error from Prowl server.',
						'code' => 500
					)
				)
			);
		}else if($data === null){
			return array(
				'Prowl' => array(
					'error' => array(
						'value' => 'OpenSSL must be enabled via PHP.ini.',
						'code' => 10000
					)
				)
			);
		}

		App::import('Core', 'Xml');
		$data = new Xml($data);
		$data = $data->toArray();
		return $data;
	}

	/*public function close(){
		parent::close();
	}*/
}
?>
<?php
class ProwlsController extends ProwlAppController {

	var $name = 'Prowls';
	var $helpers = array('Html', 'Form');
	var $uses = array('Prowl');

	function index() {
		$this->set('title_for_layout', 'Test');
	}

	function view($id = null) {
		$accountInfo = '<?xml version="1.0" encoding="UTF-8"?><prowl><success code="200" remaining="999" resetdate="1262704033" /></prowl>';
		App::import('Core', 'Xml');
		$accountInfo = new Xml($accountInfo);
		$accountInfo = $accountInfo->toArray();
		if (isset($accountInfo['Prowl']['error'])) {
			$this->Session->setFlash(__('Error retrieving account information: '.$accountInfo['Prowl']['error']['value'], true));
		}
		$this->set('prowl', $accountInfo);
	}

	function add() {
		App::import('Core', 'HttpSocket');
		$dataArray = array(
			'apikey' => 'ae8e75dfd4eaef5a56a5b3961ae6ee8987870dcc',
			'providerkey' => 'ae8e75dfd4eaef5a56a5b3961ae6ee8987870dcc',
			'priority' => 0,
			'application' => 'CakePHP Test',
			'event' => 'Testing my alerting method.',
			'description' => null
		);
		$verifyData = array(
			'apikey' => 'ae8e75dfd4eaef5a56a5b3961ae6ee8987870dcc'
		);

		$this->Prowl->create();
		if ($this->Prowl->save($dataArray)) {
			$this->Session->setFlash(__('The Prowl message has been sent.', true));
			$this->redirect(array('action'=>'index'));
		} else {
			$this->Session->setFlash(__('The Prowl message could not be sent. Please, try again.', true));
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Prowl', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Prowl->save($this->data)) {
				$this->Session->setFlash(__('The Prowl has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Prowl could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Prowl->read(null, $id);
		}
		$users = $this->Prowl->User->find('list');
		$this->set(compact('users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Prowl', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Prowl->delete($id)) {
			$this->Session->setFlash(__('Prowl deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>
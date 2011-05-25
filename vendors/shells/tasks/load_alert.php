<?php
class LoadAlertTask extends ProwlShell {
	var $uses = array('Prowl.Prowl');
	var $alert = false;
	var $outputLength = 63;

	function execute(){
		if(!function_exists('sys_getloadavg')){
			$this->out('The required internal PHP function, "sys_getloadavg()", is not available on this system.');
			return false;
		}

		$this->hr();	//separator
		$this->settings['application'] = 'Prowl LoadAlert';
		$this->out('Checking for high server load...');
		$load = sys_getloadavg();
		$current_load_avg = $load[0];
		$datetime = date('D, M j, Y \a\t g:ia');
		$loadString = implode(' ', $load);
		$this->out("Server load: $loadString");

		if($current_load_avg > 300){
			$this->settings['event'] = 'SEVERE SERVER ALERT, LOAD ABOVE 300!!';
			$this->settings['priority'] = 1;
			$this->alert = true;
		}else if($current_load_avg > 125){
			$this->settings['event'] = 'AMBER ALERT: Server Load above 125.';
			$this->alert = true;
		}else if($current_load_avg > 50){
			$this->settings['event'] = 'Notice: Server load above 50.';
			$this->alert = true;
		}else{
			$this->out('Server is running with an acceptable load.');
		}

		if($this->alert){
			$this->out($this->settings['event']);
			$this->settings['description'] = "Server Load Reported: $loadString";
			$this->settings['description'] .= "\n...as of $datetime.";
			$this->msg();
		}
		$this->hr();	//separator
		$this->out('');
	}

	function help(){
		$this->hr();
		$this->out('CakePHP Prowl Load Alert Console Task');
		$this->out('Usage: cake prowl load_alert <params values> <args>');
		$this->out("Description: This task tests the current server load and issues a Prowl message (if required) to the passed parameter list of Prowl APIs indicating the server load.");
		$this->hr();
		$this->out('Parameters:');
		$this->out("\t-silent <false>");
		$this->out('');
		$commands = array(
			'help' => 'Shows this help message.'
		);
		$arguments = array(
			'apikey' => 'Up to 5 comma separated Prowl API keys'
		);
		$this->out('Commands:');
		foreach($commands as $command => $description){
			$this->out("\t$command\n\t$description");
		}
		$this->out('');
		$this->out('Arguments:');
		foreach($arguments as $argument => $description){
			$this->out("\t$argument\n\t$description");
		}
	}
}
?>
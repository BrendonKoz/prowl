<?php
class ProwlShell extends Shell {
	var $uses = array('Prowl.Prowl');
	var $tasks = array('LoadAlert');
	var $settings = array(
		'apikey' => null,
		'priority' => 0,
		'application' => 'CakePHP Prowl Plugin',
		'event' => 'Shell Call'
	);
	var $silent = false;
	var $smartWrap = true;
	var $spacesForTabs = 3;
	var $lineLength = 63;

	function main(){
		if (empty($this->args) || $this->args[0] == '?' || strtolower($this->args[0]) == 'help') {
			$this->help();
		}
	}

	function msg(){
		if($this->args[0] == '?' || strtolower($this->args[0]) == 'help'){
			$this->help();
			return false;
		}else if(empty($this->args)){
			$this->hr();
			$this->hr();
			$this->out('ERROR: Required data missing! Please see usage:');
			$this->hr();	//help() calls hr() once at start
			$this->help();
			return false;
		}else{
			$data = null;
			$defaultLength = count($this->settings);
			$currentLength = count($this->args);

			if($defaultLength != $currentLength){
				if($defaultLength > $currentLength){
					//merge the arrays so that the numerically indexed array from incoming arguments
					//	can be set to the proper $this->settings array...
					//	...incoming parameters must be in the proper, expected order
					$data = array_slice($this->settings, 0, $currentLength-$defaultLength);
					$data = array_combine(array_keys($data), $this->args);
					$data = array_merge($this->settings, $data);
				}else{
					//incorrect parameter count
					$this->help();
					return false;
				}
			}else{
				$data = array_combine(array_keys($this->settings), $this->args);
			}
		}
		$this->Prowl->create();
		if(!$this->Prowl->save($data)){
			$this->out('The following errors occurred:');
			foreach($this->Prowl->validationErrors as $field => $err){
				if(!isset($data[$field])){
					$data[$field] = 'null';
				}
				$this->out("\t$field {value:$data[$field]}\n\t\t$err");
			}
		}
	}

	function _welcome(){
		if(isset($this->params['silent']) && !empty($this->params['silent'])){
			$this->silent = true;
		}/*else{
			parent::_welcome();
		}*/
	}

	function help(){
		$this->hr();
		$this->out('CakePHP Prowl Console Plugin');
		$this->out('Usage: cake prowl <params values> <command> <args>');
		$this->hr();
		$this->out('Parameters:');
		$this->out("\t-silent <false>");
		$this->out('');
		$commands = array(
			'help' => 'Shows this help message.',
			'msg' => 'Send a Prowl message with required argument(s).'
		);
		$arguments = array(
			'apikey' => 'Up to 5 comma separated Prowl API keys',
			'<priority>' => 'Priority level: -2, -1, 0, 1, 2',
			'<application>' => '"Application Name"',
			'<event>' => '"Event"',
			'<description>' => '"Description of Event"'
		);
		$this->out('Commands:');
		foreach($commands as $command => $description){
			$this->out("\t$command");
			$this->out("\t\t$description");
		}
		$this->out('');
		$this->out('Arguments:');
		foreach($arguments as $argument => $description){
			$this->out("\t$argument");
			$this->out("\t\t$description");
		}
	}

	function out($string, $newline = true){
		if(!$this->silent){
			//convert the array to a single string
			if(is_array($string)){
				$str = '';
				foreach ($string as $message) {
					$str .= $message ."\n";
				}
				$string = $str;
			}
			//if smart wrapping is enabled (default), modify our content
			if($this->smartWrap && $string !== ''){
				$matches = null;
				preg_match('/(^(\n*?)(\t*))[^\t].*/s', $string, $matches);
				$indentMultiplier = strlen($matches[3]);	//the found tabs
				$indent = $indentMultiplier * $this->spacesForTabs;
				$indentText = str_repeat(' ', $indent);
				$string = str_replace("\t", str_repeat(' ', $this->spacesForTabs), $string);
				$string = str_replace("\n", "\n".$indentText, $string);
				$string = wordwrap(ltrim($string), $this->lineLength - $indent, "\n$indentText");
				$string = $indentText.$string;
//				$string = str_replace($matches[1], $matches[2].str_replace($matches[3], str_repeat(' ', $indent), $matches[3]), wordwrap($matches[0], $this->lineLength - $indent, "\n"));
//				preg_match('/(?:\n*)(?:\t*)([^\t].*)/s', $string, $matches);
//				$string = preg_replace('/(\n+)(.*)/e',"'\\1'.str_repeat(' ', $indent).'\\2'", $matches[1]);
			}
			return $this->Dispatch->stdout($string, $newline);
		}
		return false;
	}
	/**
	 * Outputs a series of minus characters to the standard output, acts as a visual separator.
	 *
	 * @param boolean $newline If true, the outputs gets an added newline.
	 * @access public
	 */
	function hr($newline = false){
		if ($newline) {
			$this->out("\n");
		}
		$this->out(str_repeat('-', $this->lineLength));
		if ($newline){
			$this->out("\n");
		}
	}
}
?>
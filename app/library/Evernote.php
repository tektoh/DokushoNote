<?php

class Evernote {
	
	protected $config;
	private $log;
	
	public function __construct($config) {
		$this->config = $config;
		$this->log = new Logger();
	}
}
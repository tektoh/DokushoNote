<?php

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
	public $config;
	public $di;
	public $log;
	
	public function initialize() {
		$this->di = $this->getDI();
		$this->config = $this->di->get('config');
		$this->log = $this->di->get('logger');
		$this->session = $this->di->get('session');
	}
}
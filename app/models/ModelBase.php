<?php

use \Phalcon\Mvc\Model;

class AppModel extends Model
{
	public $config;
	public $di;
	public $log;
	
	public function initialize() {
		$this->di = $this->getDI();
		$this->config = $this->di->get('config');
		$this->log = $this->di->get('logger');
	}
}
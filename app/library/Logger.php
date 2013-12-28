<?php

class Logger {
	
	// Log type
	const NORMAL = 0;
	const DATABASE = 1;
	
	public $debug;
	public $logger;
	public $logs;
	
	public function __construct($config) {
		$this->debug = $config['debug'];
		$this->logger = new Phalcon\Logger\Adapter\File($config->logfile);
		if ($this->debug) {
			$this->logs = array();
		}
	}
	
	public function write($message, $level = Phalcon\Logger::ERROR, $type = self::NORMAL) {
		if (is_array($message)) {
			$message = print_r($message, true);
		}
		$this->logger->log($message, $level);
		
		// メッセージをメモリにバッファする
		if ($this->debug) {
			$this->logs[$type] []= array(
				'time' => time(),
				'level' => $level,
				'message' => $message,
			);
		}
	}
	
	public function debug($message, $type = self::NORMAL) {
		if ($this->debug) {
			$this->write($message, Phalcon\Logger::DEBUG, $type);
		}
	}
	
	public function info($message, $type = self::NORMAL) {
		$this->write($message, Phalcon\Logger::INFO, $type);
	}
	
	public function error($message, $type = self::NORMAL) {
		$this->write($message, Phalcon\Logger::ERROR, $type);
	}
}

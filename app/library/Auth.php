<?php

class MySessionHandler implements SessionHandlerInterface
{
	private $config;
	private $log;

	public function __construct($config) {
		$this->config = $config;
		$this->log = new Logger();
	}

	public function open($savePath, $sessionName) {
        return true;
    }

    public function close() {
        return true;
    }

    public function read($id)
    {
    	$session = Sessions::findFirst(array(
    		"conditions" => "id = :id:",
    		"bind" => array(
    			"id" => $id,
    		)
    	));
    	if (!$session) {
    		return false;
    	}
        return $session->data;
    }

    public function write($id, $data)
    {
    	if (!$id) {
    		return false;
    	}
    	$session = new Sessions();
    	$session->id = $id;
    	$session->data = $data;
    	$session->expires = time() + $this->config->expires;
    	if ($session->save() == false) {
    		$this->log->error($sessions->getMessages());
    		return false;
    	}
        return true;
    }

    public function destroy($id)
    {
    	$session = Sessions::findFirst(array(
    		"conditions" => "id = :id:",
    		"bind" => array(
    			"id" => $id,
    		)
    	));
    	$session->delete();
        return true;
    }

    public function gc($expires = null)
    {
    	if (!$expires) {
    		$expires = time();
    	} else {
    		$expires = time() - $expires;
    	}
    	$sessions = Session::find(array(
    		"conditions" => "expires < :expires:",
    		"bind" => array(
    			"expires" => $expires,
    		)
    	));
    	foreach ($sessions as $session) {
    		$session->delete();
    	}
        return true;
    }
}

class Auth
{
	public function __construct($config) {
		$this->config = $config;
		$this->log = new Logger();
		$handler = new MySessionHandler($config);
		session_set_save_handler($handler, true);
		session_start();
	}
}
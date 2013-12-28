<?php

use Guzzle\Http\Client as Client;

class AmazonECommerceService {
	
	protected $config;
	protected $apiProtocol = 'http';
	protected $apiHost = 'webservices.amazon.co.jp';
	protected $apiPath = '/onca/xml';
	protected $apiVersion = '2013-12-01';
	private $log;
	
	public function __construct($config) {
		$this->config = $config;
		$this->log = new Logger();
	}
	
	protected function signature($method, $host, $path, $params) {
		ksort($params);
		$query = "";
		$sep = "";
		foreach ($params as $key=>$val) {
			$key = rawurlencode($key);
			$val = rawurlencode($val);
			$query .= "{$sep}{$key}={$val}";
			if (empty($sep)) {
				$sep = '&';
			}
		}
		return base64_encode(hash_hmac('sha256', "{$method}\n{$host}\n${path}\n{$query}", $this->config->secretKey, true));		
	}
	
	protected function timestamp() {
		return gmdate('Y-m-d\TH:i:s\Z');;
	}
	
	protected function buildRequest($client, $method, $host, $path, $params) {
		
		$request = $client->get($path);
		$query = $request->getQuery();
		
		foreach ($params as $key => $value) {
			$query->set($key, $value);
		}
		$query->set('Signature', $this->signature($method, $host, $path, $params));
		
		return $request;
	}
	
	public function itemSearch(array $keywords, string $searchIndex = 'All') {
		
		$strKeywords = "";
		$sep = "";
		foreach ($keywords as $keyword) {
			$strKeywords .= "{$sep}{$keyword}";
			if (empty($sep)) {
				$sep = '+';
			}
		}
		
		$client = new Client("{$this->apiProtocol}://{$this->apiHost}");
		$request = $this->buildRequest($client, 'GET', $this->apiHost, $this->apiPath, array(
			'AssociateTag' => $this->config->associateTag,
			'AWSAccessKeyId' => $this->config->accessKey,
			'Keywords' => $strKeywords,
			'Operation' => 'ItemSearch',
			'SearchIndex' => $searchIndex,
			'Service' => 'AWSECommerceService',
			'Timestamp' => $this->timestamp(),
			'Version' => $this->apiVersion,
		));
		
		try {
			$response = $request->send();
		} catch (Exception $e) {
			$this->log->error($e->getMessage());
			return null;
		}
		
		$resXml = new SimpleXMLElement($response->getBody());
		
		$this->log->debug(print_r($resXml, 1));
	}
	
}

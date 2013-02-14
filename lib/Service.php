<?php

class sspmod_userregistrationApi_Service {

	public $implemented_resources = array('users');
	public $store;
	public $resources;
	private $apikey;


	public function __construct() {
		$apiconf = SimpleSAML_Configuration::getConfig('module_userregistration-api.php');

		$this->apikey = $apiconf->getString('api.key');
		$this->resources = $apiconf->getArray('api.resources');

		// Depends on userregistration module
		try {
			$this->store = sspmod_userregistration_Storage_UserCatalogue::instantiateStorage();
		}
		catch (Exception $e) {
			throw new Exception("Can't initialize the userregistration module");
		}
	}
    
	public function validateKey($key) {
		return $key == $this->apikey;
	}

	public function processRequest($resource, $method, $id, $request) {
		if (!in_array($resource, array_keys($this->resources))) {
			$this->sendResponse(405, 'Resource '.$resource.' not allowed');
		}

		if (!in_array($method, array_keys($this->resources[$resource]))) {
			$this->sendResponse(405, 'Method '.$method.' not allowed for the resource '.$resource);
		}
		
		switch ($resource) {
			case 'users':
				$user_handler = new sspmod_userregistrationApi_User($this);
				switch ($method) {                    
					case 'GET':
						$user_handler->get($id, $this->resources[$resource]['GET']);
                        break;
					case 'PUT':
						$user_handler->put($id, $request, $this->resources[$resource]['PUT']);
        				break;
				}
			    break;
		}
		$this->sendResponse(501, 'Method '.$method.' not implemented for the resource '.$resource);
	}

	public function sendResponse($status = 200, $body = '', $content_type = 'application/json')
	{
		// Set the status
		header('HTTP/1.0 ' . $status . ' ' . $this->getStatusCodeMessage($status));

		// Set caching headers
		header('Cache-Control: no-cache, must-revalidate');

		if($body != '') {
			switch($content_type) {
				case 'application/json':
					header('Content-type: application/json; charset=UTF-8');
					$body = json_encode($body);
					break;
				case 'text/html':
					header('Content-type: text/html; charset=UTF-8');
					break;
				default:
					header('Content-type: text/html; charset=UTF-8');
					break;
			}
			echo $body;
			exit;
		} else {
			// If no body, send a generic response
			header('Content-type: text/html');
		   

			$statusMessage = $this->getStatusCodeMessage($status);
			$body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
				        <html>
				            <head>
				                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
				                <title>' . $status . ' ' . $statusMessage . '</title>
				            </head>
				            <body>
				                <h1>' . $statusMessage . '</h1>
				            </body>
				        </html>';

			echo $body;
			exit;
		}
	}

	public function getStatusCodeMessage($status)
	{
		$codes = Array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '(Unused)',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'
		);

		return (isset($codes[$status])) ? $codes[$status] : '';
	}

}

?>

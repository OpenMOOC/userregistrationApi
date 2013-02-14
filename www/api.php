<?php

$api_service = new sspmod_userregistrationApi_Service();

if (!isset($_SERVER['PATH_INFO']) || $_SERVER['PATH_INFO'] == '/') {
	$api_service->sendResponse('400', 'Resource not defined');
}

$path_info_splited = explode('/', $_SERVER['PATH_INFO']);

$resource = $path_info_splited[1];

$id = '';
if( isset($path_info_splited[2])) {
	$id = $path_info_splited[2];
}

if (isset($_REQUEST['apikey'])) {
	$key = $_REQUEST['apikey'];
}
else if(isset($_SERVER['PHP_AUTH_PW'])) {
	$key = $_SERVER['PHP_AUTH_PW'];
}
else if (isset($_SERVER['HTTP_AUTHENTICATION'])) {
	$auth_data = explode(' ', $_SERVER['HTTP_AUTHENTICATION']);
	if ($auth_data[0] == 'APIKEY') {
		$key = $auth_data[1];
	}
	
}


if (isset($key) && $api_service->validateKey($key)) {
	if (!in_array($resource, $api_service->implemented_resources)) {
		$api_service->sendResponse('404', 'Resource not found');
	}

	$method = $_SERVER['REQUEST_METHOD'];

        if ($method == "PUT" || $method == "DELETE") {
            $data = file_get_contents('php://input');
            if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], 'application/json') !== FALSE) {
                $params = json_decode($data, true);
            }
            else {
                parse_str($data, $params);
		if (json_last_error() !== 0) {
			$api_service->sendResponse('400', json_encode(array('success' => FALSE)));
		}
            }
	}
	else {
		$params = $_REQUEST;
	}
	$api_service->processRequest($resource, $method, $id, $params);
}
else {
	$api_service->sendResponse('401', 'Invalid API Key');
}

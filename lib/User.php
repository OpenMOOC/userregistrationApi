<?php

class sspmod_userregistrationApi_User {

	private $service;
	private $userIdAttr;

	public function __construct($service) {
		$this->service = $service;
		$this->userIdAttr = $service->store->userIdAttr;
	}

	public function get($id, $requestedAttrs) {
		$multivalued = true;
                $userData = array();
		$user = $this->service->store->findAndGetUser($this->userIdAttr, $id, $multivalued);
		if (!empty($user)) {
			foreach($requestedAttrs as $attr) {
				if (isset($user[$attr])) {
					$userData[$attr] = $user[$attr];
				}
			}
			if (!isset($userData[$this->userIdAttr])) {
				$userData[$this->userIdAttr] = $id;
			}
			$this->service->sendResponse(200, $userData);
		}
		else {
			$this->service->sendResponse(404, array('success' => FALSE));
		}
	}

	public function put($id, $data, $requestedAttrs) {
                $userData = array();
		foreach($requestedAttrs as $attr) {
			if (isset($data[$attr])) {
				$userData[$attr] = $data[$attr];
			}
		}
                try {
			$this->service->store->updateUser($id, $userData);
			$this->service->sendResponse(200, array('success' => TRUE));
		}
		catch (Exception $e) {
			$userregistrationExceptionClass = 'sspmod_userregistration_Error_UserException';
			if ($e instanceof $userregistrationExceptionClass && $e->getMesgId() == 'uid_not_found') {
				$this->service->sendResponse(404, array('success' => FALSE));
			}
			else {
				$this->service->sendResponse(500, array('success' => FALSE));
			}
		}
	}

}

?>

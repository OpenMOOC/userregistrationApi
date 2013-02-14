<?php
/**
 * The configuration of userregistrationApi module
 * an extension of the userregistration's module that
 * offer a Rest API to update some values of the users
 */

$config = array (

	/* The API_KEY */
	'api.key' => '123456789',

	/* Array describing the resources of the API, currently only exists the user resource 
       The key of the array is an allowed HTTP method, the value is an array with the supported attributes for this method
       
       Only the attributes defined on the array will be will be considered. For ex:
         * The attributes defined in a GET method will be the attributes that will be returned (also the idAttr will be returned)
         * The attributes defined in a PUT method will be the attributes that will be changed
       
    */
	'api.resources' => array (
		'users' => array (
			'POST' => array(),
            'GET'=> array(),
            'PUT' => array(),
            'DELETE' => array()
        ),
	),

);

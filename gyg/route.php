<?php 
// gyg's standard config file.
include("config.php");

// gyg's standard helper functions.
include("functions.php");

function parseUri()
{
	global $gyg;
	
	$requestUri = $_SERVER['REQUEST_URI'];
	$requestUri = trim($requestUri, "?\ /");

	// Split the requestUri into strings and remove slashes.
	// This will always return an array of at least 1 in size.
	// If $requestUri is empty it will return [0] => "".
	$request = explode("/", $requestUri);


	// The first argument designates the page controller's ID.
	$controllerId = $request[0];
	
	// Default controller
	$defaultController = $gyg['controllers'][GYG_DEFAULT_CONTROLLER];


	// If controllerId is empty, it is assumed that the user wants to go to
	// the default controller.
	if($controllerId === '')
	{
		$gyg['controller'] = GYG_DEFAULT_CONTROLLER;
		return;
	}

	// If a user-given controller ID doesn't exist it is assumed that the user 
	// wants to access a page within the default controller.
	else if(!isset($gyg['controllers'][$controllerId]) || $gyg['controllers'][$controllerId]['enabled'] !== true)
	{
		// If this goes through the default controller is either not set
		// or is disabled in gyg's config.
		if(!isset($defaultController) || $defaultController['enabled'] !== true)
			gyg::throw404();
			

		$gyg['controller'] = GYG_DEFAULT_CONTROLLER;
		$gyg['page'] = $controllerId;
		$gyg['args'] = array_slice($request, 1);
		return;
	}


	// The controller is ok.
	$gyg['controller'] = $controllerId;


	$argCount = count($request);
	// If the request URI contains more than one arguments, set the second argument as page.
	if($argCount > 1)
		$gyg['page'] = $request[1];

	// If the request URI contains more than two arguments, put the remaining arguments into gyg's args array.
	if($argCount > 2)
		$gyg['args'] = array_slice($request, 2);

}

$gyg['controller'] = null;
$gyg['page'] = null;
$gyg['args'] = null;



// Make sure the default controller is whitelisted and enabled.
if(!isset($gyg['controllers'][GYG_DEFAULT_CONTROLLER]) || $gyg['controllers'][GYG_DEFAULT_CONTROLLER]['enabled'] !== true)
	throw new Exception('Default controller is not properly whitelisted or disabled.');

parseUri();


$controllerPath = GYG_CONTROLLERS_PATH . "{$gyg['controller']}/";

// If the controller has a config file, include it first.
if(file_exists($controllerPath . "config.php"))
	include ($controllerPath . "config.php");

// Include the controller's main PHP-file.
// For explanation of the file path structure, see gyg's config.
include($controllerPath . "main.php");
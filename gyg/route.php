<?php
/****************************************************************
 ****************************************************************
 * 
 * gyg-framework - Basic framework for web development
 * Copyright (C) 2014 Mikael Hernvall (mikael.hernvall@gmail.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 ****************************************************************
 ****************************************************************/


/*
 * Route parses the request URI into an array 
 * and includes a file depending on the contents of that
 * array. Route does not care for anything except the first
 * argument of the request URI. 
 *
 * The first argument is interpreted by Route as a 
 * controller ID in $gyg['controller']. This ID is used 
 * to access a whitelisted controller. All remaining 
 * arguments are left to the controller to interpret.
 *
 * The second argument is stored as a page ID in $gyg['page'], 
 * but a controller doesn't have to interpret it as such.
 *
 * All remaining arguments are stored as arguments in
 * $gyg['args'].
 *
 *
 * Route follows four different priority levels for
 * user-given arguments:
 *		1. If controller ID is not set or is an empty string, 
 *			route to -----> DEFAULT CONTROLLER ID.
 *
 *		2. If controller ID is set and ENABLED, route to 											
 *			route to -----> CONTROLLER ID.
 *
 *		3. If controller ID is set and DISABLED, but a shortcut 
 *		of the same ID is enabled,
 *			route to -----> SHORTCUT ID.
 *
 *		4. If the controller ID is set and DISABLED, and no
 *		shortcut of the same ID is enabled,
 *			route to -----> DEFAULT CONTROLLER ID.
 */



// gyg's standard config file.
include("config.php");

// gyg's standard helper functions.
include("functions.php");

function parseUri($requestUri)
{
	// First of all, make sure the default controller is whitelisted and enabled.
	if(!gyg::controllerIsEnabled(GYG_DEFAULT_CONTROLLER))
		throw new Exception('Default controller is not properly whitelisted or disabled.');

	
	// Get request URI and remove query sign (question mark) and trailing slashes.
	$requestUri = trim($requestUri, "?\ /");

	
	// Get the gyg variable into scope.
	global $gyg;
	
	/*
	 * If using RewriteRule, only use
	 * the request part behind the first
	 * occuring "&". 
	 */
	if($gyg['useRewriteRule'] === true)
	{
		$res = strchr($requestUri, '&');
		
		// If no "&" is found, it means the request
		// is simply "gyg/route.php", which in turn
		// means that we should go to the default controller.
		if($res === false)
		{
			$gyg['controller'] = GYG_DEFAULT_CONTROLLER;
			return;
		}
		
		$requestUri = substr($res, 1);
	}
	
	/*
	 * Split the requestUri into strings and remove all slashes.
	 * This will always return an array of at least 1 in size.
	 * If $requestUri is empty it will return [0] => "".
	 */
	$request = explode("/", $requestUri);

	
	// The first argument designates the page controller's ID.
	// $request size is always >= 1. See the comment above.
	$controllerId = isset($request[0]) ? $request[0] : '';
	


	
	/*
	 * If controllerId is empty, it is assumed that the user wants to go to
	 * the default controller. Empty arguments in the middle of the request
	 * are not bothered with.
	 */
	if($controllerId === '')
	{
		$gyg['controller'] = GYG_DEFAULT_CONTROLLER;
		return;
	}

	/*
	 * If a user-given controller ID doesn't exist it is assumed that the user 
	 * either wants to access a shortcut to a page or access a page within 
	 * the default controller.
	 */
	if(!gyg::controllerIsEnabled($controllerId))
	{
		// If the ID matches a shortcut ID, access it.
		// Shortcuts have higher priority than the default controller.
		if(gyg::shortcutIsEnabled($controllerId))
		{
			// Parse the shortcut's path just as we did at the beginning of the function
			// and replace the previous request with the shortcut's.
			$requestUri = $gyg['shortcuts'][$controllerId]['path'];
			parseUri($requestUri);
			return;
		}
		// If not, go to default controller.
		else
		{
			$gyg['controller'] = GYG_DEFAULT_CONTROLLER;
			$gyg['page'] = $controllerId;
			$gyg['args'] = array_slice($request, 1);
			return;
		}
	}


	// The controller is ok.
	$gyg['controller'] = $request[0];


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


parseUri($_SERVER['QUERY_STRING']);



$controllerPath = GYG_CONTROLLERS_PATH . "{$gyg['controller']}/";

// If the controller has a config file, include it first.
if(file_exists($controllerPath . "config.php"))
	include ($controllerPath . "config.php");

// Include the controller's main PHP-file.
// For explanation of the file path structure, see gyg's config.
include($controllerPath . "main.php");
<?php
/****************************************************************
 ****************************************************************
 * 
 * gyg-framework - Basic framework for web development
 * Copyright (C) 2014 Mikael Hernvall (mikael@hernvall.com)
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


Class GygFramework
{
	private $useRewriteRule;
	
	// Whitelists
	private $controllers;
	private $pages;
	private $shortcuts;
	
	// DEFAULT CONTROLLER
	private $defaultController;
	
	// Store the request for future use.
	private $request;
	
	// ROOT, BASE_URL, CONTROLLERS PATH
	private $root;
	private $baseUrl;
	private $controllersPath;
	
	/**
	 * Initialize gyg-framework variables.
	 *
	 * PARAMS:
	 * root, 				string - Path to
	 * the directory for gyg-framework to work in.
	 * baseUrl, 			string - Base URL path
	 * of website.
	 * defaultController, 	string - ID of default
	 * controller to route to.
	 */
	public function __construct($root, $baseUrl, $defaultController)
	{
		$this->defaultController = $defaultController;
		$this->initPaths($root, $baseUrl);
		
		$this->useRewriteRule 	= true;
		$this->controllers 		= [];
		$this->pages 			= [];
		$this->shortcuts		= [];

		$this->setRequest();
	}

	/**
	 * Initialize default paths and URLs.
	 *
	 * PARAMS:
	 * root, 	string - Path to site's root directory, as 
	 * defined by user.
	 * baseUrl,	string - The base URL path of the site. 
	 * gyg-framework will interpret all requests 
	 * relative to this URL path.
	 */
	private function initPaths($root, $baseUrl)
	{
		$this->root 			= realpath($root) . '/';
		$this->baseUrl 			= $baseUrl;
		$this->controllersPath	= $this->root . 'controllers/';
	}
	
	/**
     * Tell gyg-framework whether to use RewriteRule or not.
	 *
	 * PARAMS:
	 * flag, bool - If false, gyg-framework will use the query
	 * string for page requests. If true, gyg-framework will assume a RewriteRule
	 * has been properly set up, so that the entire request URI 
	 * can be used for page requests. 
	 */
	public function useRewriteRule($flag = true)
	{
		$this->useRewriteRule = $flag;
	}
	
	/**
	 * Insert controller into whitelist.
	 *
	 * Controller IDs that are not whitelisted will not be
	 * accessible.
	 *
	 * PARAMS:
	 * controllerId, string - ID of controller to be inserted.
	 */
	public function whitelistController($controllerId)
	{
		array_push($this->controllers, $controllerId);
	}
	
	/**
	 * Insert controllers into whitelist.
	 *
	 * PARAMS:
	 * controllerIds, string array - Controller IDs to insert.
	 */
	public function whitelistControllers($controllerIds)
	{
		foreach($controllerIds as $id)
			$this->whitelistController($id);
	}
	
	/**
	 * Insert shortcut into whitelst.
	 *
	 * A whitelisted shortcut will bind a request URI to a 
	 * keyword. For example, instead of accessing a page
	 * through 'controller/blog/cooking-mania' it may be accessed
	 * simply by 'cooking-mania' as request URI.
	 * 
	 * NOTE: gyg-framework handles shortcuts with lower priority 
	 * than controllers. If a shortcut has the same keyword as a 
	 * whitelisted controller ID, gyg->routeControl will route to
	 * the controller instead of the shortcut. Thus, make sure to use unique
	 * keywords for shortcuts.
	 *
	 * PARAMS:
	 * shortcutId, 	string - Keyword to access the shortcut by.
	 * path, 		string - Request URI.
	 */
	public function whitelistShortcut($shortcutId, $path)
	{
		$this->shortcuts[$shortcutId] = $path;
	}
	
	
	
	/**
	 * Insert shortcuts into whitelist.
	 *	 
	 * PARAMS:
	 * shortcuts, array - Array containing shortcut elements
	 * in the following format:
	 * 		'shortcutId' => 'requestUri'
	 * where shortcutId is the keyword to access the shortcut
	 * by, and requestUri is the request URI pointing to a page.
	 */
	public function whitelistShortcuts($shortcuts)
	{
		foreach($shortcuts as $id => $path)
			$this->whitelistShortcut($id, $path);
	}
	
	/**
	 * Insert page into whitelist.
	 * 
	 * PARAMS:
	 * pageId, string - ID of page to insert into whitelist.
	 */
	public function whitelistPage($pageId)
	{
		array_push($this->pages, $pageId);
	}
	
	/** 
	 * Insert pages into whitelist.
	 * 
	 * PARAMS:
	 * pageIds, string array - Array containing IDs of
	 * pages to insert into whitelist.
	 */
	public function whitelistPages($pageIds)
	{
		foreach($pageIds as $id)
			$this->whitelistPage($id);
	}
	
	/**
	 * Parse request, extract path to controller's
	 * main file from parsing output and include it.
	 *
	 * RETURNS:
	 * string - Path to main file of requested controller.
	 */
	public function routeControl()
	{
		/*
		 * Parse either the request URI or the query string, depending on
		 * whether we want use RewriteRule or not.
		 */
		$request = $this->useRewriteRule === true ? $this->parseRequestUri() : $this->parseQueryString();

		// Parse the request into $this->request array.
		$this->parseRequest($request);

		// Return path to main file of requested controller.
		return $this->getControllerMainPath($this->request['controller']);
	}
	
	/**
	 * Set gyg-framework's request variable.
	 *
	 * PARAMS:
	 * (controllerId), string - ID of controller.
	 * (args), string array - Array containing request parts
	 * succeeding controller ID.
	 */
	private function setRequest($controller = '', $args = [])
	{
		$this->request = 
		[
			'controller' 	=> $controller,
			'args'			=> $args,
			'argCount'		=> count($args),
		];
	}
	
	/**
	 * Get gyg-framework's request variable.
	 *
	 * RETURNS:
	 * string array - Array containing controller ID, request parts
	 * succeeding controller ID and the count of these parts in the
	 * following format:
	 * [
	 *		'controller' 	=> $controllerId,
	 * 		'args'		 	=> $args,
	 *		'argCount'		=> $argCount,
	 * ]
	 */
	public function getRequest()
	{
		return $this->request;
	}
	
	/**
	 * Interpret request and set gyg-framework's
	 * request variable accordingly.
	 *
	 * PARAMS:
	 * request, string array - Parts of request.
	 */
	private function parseRequest($request)
	{
		// First of all, make sure the default controller is enabled.
		if(!$this->controllerIsEnabled($this->defaultController))
			throw new Exception('Default controller is not whitelisted. Please whitelist it by using gyg->whitelistController.');


		// The first argument designates the controller's ID.
		$controllerId = $request[0];
		
		// The remaining arguments are stored in a separate array.
		$args = array_slice($request, 1);
	
		// If no controller ID is set. Give control to default controller.
		if($controllerId === '')
		{
			$this->setRequest($this->defaultController);
			return;
		}
				
		/*
		 * If a user-given controller ID doesn't exist or isn't enabled,
		 * check if there is a shortcut by that ID. If not, let default
		 * controller interpret request.
		 */
		if(!$this->controllerIsEnabled($controllerId))
		{
			/*
			 * If shortcut is found. Redo the parsing operation with
			 * the shortcut's path as request.
			 */
			if($this->shortcutIsEnabled($controllerId))
				$this->parseRequest($this->shortcuts[$controllerId]);
			// If not, let default controller interpret request.
			else
				$this->setRequest($this->defaultController, $args);
			
			return;
		}

		/*
		 * If we've come this far it means the controller is ok. 
		 * We will not perform any parsing on the remaining arguments.
		 * That work is left to the controller.
		 */
		$this->setRequest($controllerId, $args);
	}
	
	/**
	 * Extract request path from request URI by 
	 * removing base URL path, query string and trimming 
	 * away query trailing URL symbols. Explode result 
	 * into array, using slashes as separators.
	 *
	 * RETURNS:
	 * string array - Parts of exploded request path.
	 */
	private function parseRequestUri()
	{	
		$request = $_SERVER['REQUEST_URI'];
		
		// Remove base url from beginning.
		$request = str_replace($this->getBaseUrl(), '', $request);

		// Remove trailing query sign (question mark) and slashes.
		$request = trim($request, "?\ /");

		// Remove query string from request URI.
		$request = str_replace('?' . $_SERVER['QUERY_STRING'], '', $request);
		
		// Lastly, explode the request into an array.
		$request = explode('/', $request);
		

		return $request;
	}
	
	/**
	 * Clean up query string by trimming away
	 * trailing URL symbols and exploding
	 * result into array, using slashes as
	 * separators.
	 *
	 * RETURNS:
	 * string array - Parts of exploded query string.
	 */
	private function parseQueryString()
	{
		$query = $_SERVER['QUERY_STRING'];

		// Remove trailing query sign (question mark) and slashes.
		$query = trim($query, "?\ /");
		
		/*
		 * Split the query into array and remove all slashes.
		 */
		$query = explode("/", $query);
			
		return $query;
	}
	
	/**
	 * Include a file silently by saving it
	 * in the output buffer.
	 *
	 * PARAMS:
	 * path, string - Path to file to include.
	 *
	 * RETURNS:
	 * string - Content of output buffer after
	 * inclusion of $path.
	 */
	public function silentInclude($path)
	{
		ob_start();
		include($path);
		return ob_get_clean();
	}
	
	/**
	 * Render a template file by performing
	 * extract on its variables and then include
	 * file.
	 *
	 * PARAMS:
	 * path, 	string 	- Path to template file.
	 * (vars), 	array	- Variables used in template file.
	 */
	public function render($path, $vars = [])
	{
		extract($vars);
		include($path);
	}

	/**
	 * Get path to a controller's main file, assuming
	 * it exists.
	 *
	 * PARAMS:
	 * controllerId, string - ID of controller.
	 *
	 * RETURNS:
	 * string - Path to main file of controller by the id
	 * given in parameter.
	 */
	private function getControllerMainPath($controllerId)
	{
		return $this->controllersPath . $controllerId . '/main.php';
	}


	/**
	 * Check if controller is enabled by checking
	 * if it has been inserted into the 
	 * controller whitelist using gyg->whitelistController.
	 *
	 * PARAMS:
	 * controllerId, string - Controller ID as defined when
	 * whitelisting the controller using gyg->whitelistController
	 *
	 * RETURN:
	 * bool - True if controller ID exists in whitelist, else false.
	 */
	public function controllerIsEnabled($controllerId)
	{
		return in_array($controllerId, $this->controllers);
	}
	
	/**
	 * Check if page is enabled by checking
	 * if it has been inserted into the 
	 * page whitelist using gyg->whitelistPage.
	 *
	 * PARAMS:
	 * pageid, string - Page ID as defined when
	 * whitelisting the page using gyg->whitelistPage
	 *
	 * RETURN:
	 * bool - True if page ID exists in whitelist, else false.
	 */
	public function pageIsEnabled($pageId)
	{
		return in_array($pageId, $this->pages);
	}
	
	/**
	 * Check if shortcut is enabled by checking
	 * if it has been inserted into the 
	 * shortcut whitelist using gyg->whitelistShortcut.
	 *
	 * PARAMS:
	 * pageid, string - Shortcut ID as defined when
	 * whitelisting the shortcut using gyg->whitelistShortcut
	 *
	 * RETURN:
	 * bool - True if shortcut ID exists in whitelist, else false.
	 */
	public function shortcutIsEnabled($shortcutId)
	{
		return isset($this->shortcuts[$shortcutId]);
	}

	/**
	 * Get gyg-framework's base URL path variable.
	 *
	 * RETURNS:
	 * string - Base URL path as defined by user.
	 */
	public function getBaseUrl()		{return $this->baseUrl;}
	
	/**
	 * Get gyg-framework's root variable.
	 *
	 * RETURNS:
	 * string - Path to root as defined by user.
	 */
	public function getRoot()			{return $this->root;}
	
	/**
	 * Get path to controllers directory.
	 *
	 * RETURNS:
	 * string - Path to controllers directory.
	 */
	public function getControllersPath(){return $this->controllersPath;}
	
	
	/**
	 * Create an URL path from a file path that points to a file using the file controller.
	 *
	 * PARAMS:
	 * filePath, string - Path to file to create URL path from.
	 *
	 * RETURNS:
	 * string - URL path pointing to the file through the file controller.
	 */
	public function path2url($filePath)
	{
		$originalFilePath = $filePath;
	
		// Resolve path references like "." and ".."
		$filePath = realpath($filePath);

		// Remove ROOT from file path.
		$filePath = str_replace($this->getRoot(), '', $filePath);
		
		// Remove trailing slashes.
		$filePath = trim($filePath, '/');
		
		// Make array.
		$pathArray = explode('/', $filePath);
		
		$arrayCount = count($pathArray);
		
		
		
		// Only allow linking to directories below controllers directory.
		if($pathArray[0] !== basename($this->getControllersPath()))
			throw new Exception('gyg->path2url (main.php): Invalid path: "' . $originalFilePath . '". Only files below "controllers" directory allowed.');
			
		unset($pathArray[0]);


		$url = "/file/" . implode('/', $pathArray);
		
		// If not using RewriteRule, simply prepend a query sign.
		if($this->useRewriteRule === false)
			$url = '?' . $url;
			
		return $url;
	}
	
};
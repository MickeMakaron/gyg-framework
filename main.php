<?php
/****************************************************************
 ****************************************************************
 * 
 * gyg-framework - Basic framework for web development
 * Copyright (C) 2014-2015 Mikael Hernvall (mikael.hernvall@gmail.com)
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
	
	private $baseUrl;
	private $controllersPath;
	
	/**
	 * \brief Constructor
	 *
	 * Initialize gyg-framework variables.
	 *
	 * \param controllersPath string Path to
	 * the controllers directory.
	 * \param baseUrl string Base URL path
	 * of website.
	 * \param defaultController string ID of default
	 * controller to route to.
	 */
	public function __construct($controllersPath, $baseUrl, $defaultController)
	{
		$this->defaultController = $defaultController;
		
		$this->baseUrl 			= $baseUrl;
		$this->controllersPath	= rtrim(realpath($controllersPath), '/');
		$this->useRewriteRule 	= true;
		$this->controllers 		= [];
		$this->pages 			= [];
		$this->shortcuts		= [];

		$this->setRequest();
	}

	
	/**
     * \brief Tell gyg-framework whether to use RewriteRule or not.
	 *
	 * \param flag bool If false, gyg-framework will use the query
	 * string for page requests. If true, gyg-framework will assume a RewriteRule
	 * has been properly set up, so that the entire request URI 
	 * can be used for page requests. 
	 */
	public function useRewriteRule($flag = true)
	{
		$this->useRewriteRule = $flag;
	}
	
	/**
	 * \brief Insert controller into whitelist.
	 *
	 * Controllers that are not whitelisted will not be
	 * accessible.
	 *
	 * \param controllerId string ID of controller to be whitelisted.
	 */
	public function whitelistController($controllerId)
	{
		array_push($this->controllers, $controllerId);
	}
	
	/**
	 * \brief Insert controllers into whitelist.
	 *
	 * \param controllerIds string array Controller IDs to insert.
	 */
	public function whitelistControllers($controllerIds)
	{
		foreach($controllerIds as $id)
			$this->whitelistController($id);
	}
	
	/**
	 * \brief Insert shortcut into whitelst.
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
	 * \param shortcutId string Keyword to access the shortcut by.
	 * \param path string Request URI.
	 */
	public function whitelistShortcut($shortcutId, $path)
	{
		$this->shortcuts[$shortcutId] = $path;
	}
	
	
	
	/**
	 * \brief Insert shortcuts into whitelist.
	 *	 
	 * \param shortcuts array Array containing shortcut elements
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
	 * \brief Insert page into whitelist.
	 * 
	 * \param pageId string ID of page to insert into whitelist.
	 */
	public function whitelistPage($pageId)
	{
		array_push($this->pages, $pageId);
	}
	
	/** 
	 * \brief Insert pages into whitelist.
	 * 
	 * \param pageIds string array Array containing IDs of
	 * pages to insert into whitelist.
	 */
	public function whitelistPages($pageIds)
	{
		foreach($pageIds as $id)
			$this->whitelistPage($id);
	}
	
	/**
	 * \brief Route control to controller
	 *
	 * Parse request, extract path to controller's
	 * main file from parsing output and return it.
	 *
	 * \return string Path to main file of requested controller.
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
	 * \brief Set gyg-framework's request variable.
	 *
	 * \param controllerId string ID of controller.
	 * \param args string array Array containing request parts
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
	 * \brief Get gyg-framework's request variable.
	 *
	 * \param string array Array containing controller ID, request parts
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
	 * \brief Interpret request and set gyg-framework's
	 * request variable accordingly.
	 *
	 * \param request string array Parts of request.
	 */
	private function parseRequest($request)
	{
		// First of all, make sure the default controller is enabled.
		if(!$this->controllerIsWhitelisted($this->defaultController))
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
		if(!$this->controllerIsWhitelisted($controllerId))
		{
			/*
			 * If shortcut is found. Redo the parsing operation with
			 * the shortcut's path as request.
			 */
			if($this->shortcutIsWhitelisted($controllerId))
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
	 * \brief Extract request path from request URI
	 *
	 * Extract request path from request URI by 
	 * removing base URL path, query string and trimming 
	 * away query trailing URL symbols. Explode result 
	 * into array, using slashes as separators.
	 *
	 * \return string array Parts of exploded request path.
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
	 * \brief Parse query string
	 * 
	 * Clean up query string by trimming away
	 * trailing URL symbols and exploding
	 * result into array, using slashes as
	 * separators.
	 *
	 * \return string array Parts of exploded query string.
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
	 * \brief Get path to a controller's main file.
	 *
	 * \param controllerId string ID of controller.
	 *
	 * \return string Path to main file of controller by the id
	 * given in parameter.
	 */
	private function getControllerMainPath($controllerId)
	{
		return $this->controllersPath .'/'. $controllerId . '/main.php';
	}


	/**
	 * \brief Check if controller is whitelisted.
	 *
	 * Check if controller is whitelisted by checking
	 * if it has been inserted into the 
	 * controller whitelist using gyg->whitelistController.
	 *
	 * \param controllerId string Controller ID as defined when
	 * whitelisting the controller using gyg->whitelistController
	 *
	 * \return bool True if controller ID exists in whitelist, else false.
	 */
	public function controllerIsWhitelisted($controllerId)
	{
		return in_array($controllerId, $this->controllers);
	}
	
	/**
	 * \brief Check if page is whitelisted. 
	 *
	 * Check if page is enabled by checking
	 * if it has been inserted into the 
	 * page whitelist using gyg->whitelistPage.
	 *
	 * \param pageid string Page ID as defined when
	 * whitelisting the page using gyg->whitelistPage
	 *
	 * RETURN:
	 * bool - True if page ID exists in whitelist, else false.
	 */
	public function pageIsWhitelisted($pageId)
	{
		return in_array($pageId, $this->pages);
	}
	
	/**
	 * \brief Check if shortcut is whitelisted.
	 *
	 * Check if shortcut is whitelisted by checking
	 * if it has been inserted into the 
	 * shortcut whitelist using gyg->whitelistShortcut.
	 *
	 * \param pageid string Shortcut ID as defined when
	 * whitelisting the shortcut using gyg->whitelistShortcut
	 *
	 * \return bool True if shortcut ID exists in whitelist, else false.
	 */
	public function shortcutIsWhitelisted($shortcutId)
	{
		return isset($this->shortcuts[$shortcutId]);
	}

	/**
	 * \brief Get gyg-framework's base URL path variable.
	 *
	 * \return string Base URL path as defined by user.
	 */
	public function getBaseUrl()		{return $this->baseUrl;}
	
	
	/**
	 * \brief Get path to controllers directory.
	 *
	 * \return string Path to controllers directory.
	 */
	public function getControllersPath(){return $this->controllersPath;}
	
	
	/**
	 * \brief Create an URL path from a file path.
	 *
	 * Create an URL path from a file path that points to a file using the file controller.
	 * 
	 * \param filePath string Path to file to create URL path from.
	 *
	 * \throw Exception if filePath parameter is not below controllers directory.
	 * 
	 * \return string URL path pointing to the file through the file controller.
	 */
	public function path2url($filePath)
	{
		$originalFilePath = $filePath;
	
		// Resolve path references like "." and ".."
		$filePath = realpath($filePath);

		// Make sure filePath is below controllers directory.
		if(strpos($filePath, $this->controllersPath))
				throw new Exception("Invalid path: '{$originalFilePath}'. Only files below '{$this->controllersPath}' directory allowed.");
				
		// Remove controllers directory path from filePath.
		$filePath = str_replace($this->controllersPath, '', $filePath);
		
		// Remove trailing slashes.
		$filePath = trim($filePath, '/');
		
		
		$url = "/file/{$filePath}";
		
		// If not using RewriteRule, simply prepend a query sign.
		if($this->useRewriteRule === false)
			$url = '?' . $url;
			
		return $url;
	}
	
};
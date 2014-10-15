<?php
/*
 * gyg helper functions
 */
 
// Enclose functions in gyg namespace.
Class gyg
{
	/*
	 * Create a pretty file URL.
	 * If the file lies in the controller's directory, only pass the controller.
	 * If the file lies in the page's directory, pass both controller and page.
	 */
	static function createFileUrl($filePath, $controller, $page = null)
	{
		if($page === null)
			return BASE_URL . "?file/{$controller}/{$filePath}";
		else
			return BASE_URL . "?file/{$controller}/{$page}/{$filePath}";
	}

	// Whine about lost stuff and then go die.
	static function throw404()
	{
		header('HTTP/1.0 404 Not Found');
		include(ERROR404);
		die();
	}
	
	/*
	 * Check if the controller ID of a controller exists, is enabled
	 * and is whitelisted properly in $gyg['controllers'].
	 */
	static function controllerIsEnabled($controllerId)
	{
		global $gyg;
		
		// Is the controller ID even registered in the gyg config?
		if(!isset($gyg['controllers'][$controllerId]))
			return false;
			
		// Yep, it's registered.
		$controller = $gyg['controllers'][$controllerId];
		
		// Throw exception if the controller doesn't have a "enabled" property.
		if(!isset($controller['enabled']))
			throw new Exception('gyg::controllerIsEnabled (functions.php): Controller is not properly registered in gyg config. "Enabled" property is missing.');
			
		return ($controller['enabled'] === true);
	}
	
	/*
	 * Check if the page ID of a controller exists, is enabled and is 
	 * whitelisted properly in $gyg['controllers'].
	 *
	 * pageIsEnabled uses the $gyg['pages'] array. If the controller
	 * doesn't make use of this array as a whitelist, this function will
	 * not work.
	 *
	 * NOTE: This function only works at the controller stage or below
	 * because that's where the $gyg['pages'] variable is set.
	 */
	static function pageIsEnabled($controllerId, $pageId)
	{
		global $gyg;
		
		// Make sure controller is registered in gyg's config.
		if(!isset($gyg['controllers'][$controllerId]))
			throw new Exception('gyg::pageIsEnabled (functions.php): Controller ID does not exist.');
		
		// Make sure the controller makes use of the standard page whitelist array.
		if(!isset($gyg['pages']))
			throw new Exception(	'gyg::pageIsEnabled (functions.php): Page whitelist not defined. 
									To use this function the controller must make use of a page whitelist 
									in its config file. For examples, see the gyg or the file controllers.');
			
		$pages = $gyg['pages'];
		
		if(isset($pages[$pageId]))
		{
			// Make sure the controller's page whitelist is properly defined.
			if(!isset($pages[$pageId]['enabled']))
				throw new Exception('gyg::pageIsEnabled (functions.php): Whitelist attribute is missing "enabled" attribute. For examples, see the gyg or the file controllers.');
				
			return $pages[$pageId]['enabled'] === true;
		}
		else
			return false;
	}
	
	/*
	 * Check if the shortcut ID of a shortcut exists, is enabled and
	 * is whitelisted properly in $gyg['shortcuts'].
	 */
	static function shortcutIsEnabled($shortcutId)
	{
		global $gyg;
		
		// Is the shortcut ID even registered in the gyg config?
		if(!isset($gyg['shortcuts'][$shortcutId]))
			return false;
			
		// Yep, it's registered.
		$shortcut = $gyg['shortcuts'][$shortcutId];
		
		// Throw exception if the shortcut doesn't have a "enabled" property.
		if(!isset($shortcut['enabled']))
			throw new Exception('gyg::shortcutIsEnabled (functions.php): Shortcut is not properly registered in gyg config. "Enabled" property is missing.');
			
		// Throw exception if the shortcut doesn't have a "path" property.
		if(!isset($shortcut['path']))
			throw new Exception('gyg::shortcutIsEnabled (functions.php): Shortcut is not properly registered in gyg config. "Path" property is missing.');
			
		return ($shortcut['enabled'] === true);
	}
};
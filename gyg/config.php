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
 * This config file is used by all pages on the getyourgame domain.
 * It is possible to create individual config files for pages as well,
 * but keep in mind that it will be included after this one.
 */ 
 
/*
 * Settings for PHP error-reporting.
 */
error_reporting(-1);              // Report all type of errors
ini_set('display_errors', 1);     // Display all errors 
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly	


/*******************************************
 * MANDATORY USER CONFIGURATION
 * These definitions must be adjusted to fit the user's environment:
 */
 
/*
 * BASE_URL
 * The base URL of your site.
 */
define('BASE_URL', "");

/*
 *
 * GYG_DEFAULT_CONTROLLER
 * Default controller ID that you want to use.
 * If a queried controller ID is not recognized,
 * gyg will redirect to the default controller.
 * This means you will be able to access your default
 * controller's pages like this:
 * 		www.site.com?page/arg1/arg2...
 * instead of this:
 * 		www.site.com?controller/page/arg1/arg2...
 */
define('GYG_DEFAULT_CONTROLLER', 'example');

/*******************************************/


/*
 * gyg configuration array.
 * All variables $gyg contains are used in rendering the page.
 * 
 * The file structure is as follows:
 * config <- route -> page -> render -> page template,
 * The route (route.php) includes gyg's config and processess the url
 * into the array $gyg['request']. It then includes the corresponding page
 * which is the actual file that manages what the user sees. When the page
 * has done its thing it includes gyg's render function, which renders
 * the content set by page according to the page's template.

 *
 * THE CONFIG FILE (config.php) contains standard settings that covers
 * most pages' needs and is included in the page file.
 * If you want to define your own configurations, just make your own 
 * config file and include it in your page file.
 *
 * THE PAGE FILE contains the content of a particular page. For example, if you want
 * a page's title to be "Fart", you could set $gyg['title'] to "Fart". Raw HTML-strings are
 * used for content shown on the page. If you want your header contain a certain picture,
 * you could set $gyg['header'] to "<img src='img.jpg'></img>". For larger HTML-strings
 * it is recommended that you split your page file into several files and then import their content
 * with the file_get_contents function. For example, you could make a file called "menu.php" for
 * your menu and then set $gyg['menu'] to file_get_contents(pathToMenuPHP). However, if your
 * file contains more than HTML, i.e. PHP, you will need to set the $gyg['menu'] in menu.php
 * and then simply include menu.php in your main PHP-file.
 *
 * THE RENDER FILE simply extracts all the variables from the gyg array and then includes the
 * page's template file.
 *
 * THE TEMPLATE FILE defines the page's layout. I.e. where and how the gyg variables 
 * defined in the page file will be shown on the page.
 */
$gyg = [];


/*
 * If set to false, gyg-framework will rewrite URLs by
 * using query strings (the part of the URL after a question mark).
 * 
 * If set to true, gyg-framework will rewrite URLs by
 * using the request URI. When creating a RewriteRule, reroute
 * all requests  to route.php, like so:
 *		<Directory path/to/your/website/root>
 *			RewriteEngine On
 *			RewriteRule (.*) gyg/route.php?$1 [NC,QSA,L]
 *		</Directory>			
 *			
 *
 * If you don't know anything about this kind of stuff and/or
 * don't have access to your web servers settings, keep
 * it set to false. The upside of RewriteRule, however,
 * is that you no longer will have a question mark in 
 * your URLs.
 *
 * NOTE: gyg-framework has no control over AccessFileName files
 * or other directives in your web servers configuration files. 
 * You must set this up yourself.
 *
 * NOTE2: If set to true, URLs linking through the query string
 * system will not work.
 */
$gyg['useRewriteRule'] = false;


/*
 * Whitelist of page controllers.
 *
 * When creating a new controller, it must be located in the "controller"
 * folder and its folder must have the same name
 * as the controller's index. Additionally, the folder must contain 
 * a PHP-file named "main". 
 * For example, if you want to create a new controller
 * named "banana", the controller's main PHP-file must
 * have the following path structure:
 * 		controllers/banana/main.php
 *
 * When registering a new controller, you must
 * add it to the $gyg['controllers'] array in the following
 * manner:
 * 		'controllerId' => ['enabled' => true]
 * The 'enabled' property is for easy disabling of controllers,
 * without having to completely remove them from the whitelist.
 * A disabled controller is inaccessible and will be regarded
 * by gyg-framework as not whitelisted.
 *
 * Whitelisting a controller without following the above structure
 * will cause gyg-framework to throw exceptions.
 */
$gyg['controllers'] = 
[
	'example' 	=> ['enabled' => true],
	'file'		=> ['enabled' => true]
];


/*
 * Shortcuts!
 * 
 * If you want a page request to be accessible by a keyword,
 * you can define it here. 
 *
 * For example, if you want to access a blog named "Cooking Mania" 
 * without having to go through this potentially lengthy request:
 * 		site.com?controller/cooking-mania
 * you can set a shortcut to it, allowing you to access it
 * through this request:
 *		site.com?cooking-mania
 *
 * To register a shortcut, add it to the $gyg['shortcuts'] array in
 * this form:
 * 		'shortcutId' => ['enabled' => true, 'path' => 'controller/page/arg1/arg2/...']
 * 
 * The 'enabled' property is for easy disabling of shortcuts,
 * without having to completely remove them from the whitelist.
 * A disabled shortcut is inaccessible and will be regarded
 * by gyg-framework as not registered.
 * 
 * The 'path' property is the page's real request URI.
 *
 * Whitelisting a shortcut without following the above structure
 * will cause gyg-framework to throw exceptions.
 * 
 * NOTE: A shortcut has lower priority than a controller. If a shortcut has
 * the same ID as a whitelisted controller, gyg-framework will route to
 * the controller, instead of the shortcut. Thus, make sure to use unique
 * IDs.
 */
$gyg['shortcuts'] =
[
	'shortcut' => ['enabled' => true, 'path' => ''],
];
 



/*
 * Define paths.
 */

// Path to gyg's render file.
define('GYG_RENDER_PATH', __DIR__ . '/render.php');
 
// Path to root.
define('ROOT', __DIR__ . '/../');
 
// Path to controller directory.
define('GYG_CONTROLLERS_PATH', __DIR__ . '/../' . 'controllers/');

// Path to 404 document.
define('ERROR404', ROOT . "gyg/404.php");

 
 
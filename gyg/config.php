<?php 
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
 * Whitelist of page controllers.
 *
 * When creating a new controller, it must be located in the "controller"
 * folder and its folder must have the same name
 * as the controller's index. Additionally, the folder must contain 
 * a PHP-file named "main". 
 * For example, if you want to create a new controller
 * named "banana" with a page called "monkey", the page's main PHP-file must
 * have the following path structure:
 * "controllers/banana/pages/monkey/monkey.php".
 * The controller's main file would look like this:
 * "controllers/banana/main.php"
 *
 */
$gyg['controllers'] = 
[
	'example' 	=> ['enabled' => true],
	'file'		=> ['enabled' => true]
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

 
 
gyg-framework
=============
##Introduction
gyg-framework is a very simple and lightweight framework for easy setup of controllers and pretty URLs. It's essentially an
URL parser with some additional features.

Note that gyg-framework
* does not use .htaccess.
* is fully PHP.
* has no library dependancies (except standard PHP).

Features include:
* URL parsing
* MVC-like rendering
* URL shortcuts

##Setup
* Extract into the website's root directory.
* Set the mandatory configuration options in "gyg/config.php".
* Done!
 

##URL parsing
The gyg-framework is a very basic framework for MVC- and web development. The framework allows the user to 
create individual front-controllers with their own pages and argument interpretation. The workflow is as follows:

Example URL: www.site.com?controller/page/arg1/arg2...

1. Parse the query string "?controller/page/arg1/arg2..." into an array of the following structure:
  * $gyg['controller'] = controller
  * $gyg['page'] = page
  * $gyg['args'] = [arg1, arg2, ...]
	
2. Redirect to the controller and let it interpret the remaining arguments.

That's it! The controller alone decides how to interpret remaining arguments. This means there is complete freedom when creating a controller.
The workflow within a controller usually looks like this:
1. The controller interprets the page argument ($gyg['page']) and redirects to a page.
2. The page interprets the remaining arguments ($gyg['args']) and does something with them.


##Rendering
Additionally, the gyg-framework offers an MVC-structured rendering model. This is entirely located in the page. To make use of it, the page's file
structure is as follows:

MODEL FILE:
Contains all the logic and sets the actual content of the page.

TEMPLATE FILE:
Contains the structure of the page.

To use gyg-framework's rendering, in your model file, simply set the path of your template file to 

    $gyg['templatePath'] = 'absolutePathToTemplate' 
and include gyg-framework's render path at the end.

    include(GYG_RENDER_PATH);
    
    
##Shortcuts
To avoid lengthy URLs, gyg-framework can associate a request URI to a shortcut ID. Have, for example, a page with the following request URI:

	?controller/page/arg1
can be shortened to:

	?shortcutID

To do this, simply add the following to gyg's shortcut array in gyg's config file:

	'shortcutID' => ['enabled' => true, 'path' => 'controller/page/arg1']

Now gyg-framework will interpret "?shortcutID" as "?controller/page/arg1". Note, however,
that shortcuts have lower priority than controllers. If an enabled shortcut and controller share
the same ID, gyg-framework will prioritize the controller. For this reason, try to use unique IDs.
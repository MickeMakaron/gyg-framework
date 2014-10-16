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


/* Main controller document.
 * 
 * This main document defines how this controller interprets incoming
 * arguments parsed by gyg/route.php. All arguments are guaranteed to be set
 * by gyg/route.php. If an argument is not given by the user, it is set as null.
 *
 * Argument structure:
 * 		www.website.com?controller/page/arg1/arg2/arg3...
 *
 * Arguments are set as follows:
 * CONTROLLER 
 * ID of the requested controller. It is saved in $gyg['controller'].
 * You typically don't need to handle it at this level. To see it in use, see
 * gyg/route.php.
 *
 * PAGE
 * ID of the requested page. It is saved in $gyg['page'].
 * How your controller is going to handle this ID is entirely up to the author(s)
 * of this document's controller.
 * It is recommended that you use a whitelist for your pages. See the config file 
 * of the gyg controller for an example.
 *
 * ARGS
 * Arguments to be interpreted either by the controller or individual pages.
 * They are saved in $gyg['args']. If your controller contains a blog, an 
 * argument could be used like this:
 * 		www.website.com?controller/blog/title
 * where "blog" is the ID of your blog page and "title" is the ID of a 
 * blog post. See the "img" page on the file controller for an example.
 */

$page = $gyg['page'];

// If page argument is not set, go to homepage.
if($page === null)
{
	$gyg['page'] = 'home';
	include("pages/home/home.php");
}
else
{
	// If page argument is not whitelisted or or enabled, throw 404.
	if(!gyg::pageIsEnabled($gyg['controller'], $page))
		gyg::throw404();
		
	// If we came this far it means the page's fine n' dandy
	include("pages/{$page}/{$page}.php");
}



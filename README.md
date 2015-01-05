![gyg-framework](https://raw.githubusercontent.com/MickeMakaron/gyg-framework/master/controllers/example/img/gyg.png) gyg-framework
=============
##Introduction
gyg-framework is a very simple and lightweight framework for easy setup of controllers and pretty URLs. It's essentially an
URL parser with some additional features.

Note that gyg-framework
* supports both querystring- and rewrite-based URL-parsing.
* is fully PHP.
* is self-contained.

Features include:
* Shortcuts

##Setup
Install files anywhere. Put contents of _controllers_ directory into user-defined _controllersPath_ (see below).

Declare gyg-framework object and configure its settings. Here follows a version of [Playhouse](http://mikael.hernvall.com/playhouse "Playhouse") as an example:

    $gyg = new GygFramework($controllersPath, $baseUrl, $defaultController);
    $gyg->useRewriteRule($useRewriteRule);
    $gyg->whitelistControllers($controllers);
    $gyg->whitelistShortcuts($shortcuts);
    
* _controllersPath_ is the path to the controllers directory. For controller setup, see **Controller setup** below.
* _baseUrl_ is the base URL of the website that gyg-framework is to work in.
* _defaultController_ is the name of the controller to route to when the URI request does not point to a whitelisted controller.
* _useRewriteRule_ sets whether to use URL-rewriting or not.
* _controllers_ names of controllers to whitelist.
* _shortcuts_ shortcuts to whitelist.


Done!

###Controller setup
####Whitelisting
gyg-framework will only route to a controller if it is whitelisted using _whitelistController_ or _whitelistControllers_. To whitelist a controller, simply call _whitelistController_ with the controller's name as argument.

    $gyg->whitelistController('myController');

####File structure
The controller must strictly adhere to two rules. First, the controller's files must be located in a folder below the _controllersPath_ defined when constructing the _GygFramework_ object. The folder name must be identical to the string used when whitelisting the controller. 
    
    controllersPath/controllerName/
    
Second, in the controller's directory, there must exist a file called _main.php_. This file is what gyg-framework will route to.

##Features
###URL parsing
The gyg-framework is a very basic URL parser. The framework allows the user to 
create individual front-controllers with absolute freedom. During this section it is assumed that _useRewriteRule_ is set to true. The workflow is as follows:

User accesses site by the URL below.

    www.site.com/controller/arg1/arg2...

Parse the request URI

    /controller/arg1/arg2

into 

    $request['controller'] = controller;
    $request['args'] = [arg1, arg2, ...];

If controller is whitelisted and its main file exists, return the path to its main file. If controller is not whitelisted, return the path to default controller's main file.

That's it! The controller alone decides how to interpret remaining arguments.
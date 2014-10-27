<?php
// Hobo-fix for disabling index browsing without .htaccess.
// An index.php file should exist in ALL directories.

// Do the exact same thing as gyg's standard helper function "throw404".
header('HTTP/1.0 404 Not Found');
include($_SERVER['DOCUMENT_ROOT'] . "/gyg/404.php");
die();
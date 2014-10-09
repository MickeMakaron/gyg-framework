<?php
/*
 * The File controller creates a good-looking linking structure for files.
 * If you want to link to a file, simply link to it using gyg's createImgUrl function. 
 * The visible link will have the format "file/controller/filePath" if in the
 * controller's directory, and "file/controller/page/filePath" if
 * in the page's directory. 
 *
 * The page argument is interpreted as the controller containing the file.
 * Furthermore, at least 1 argument is required. The minimum is thus this:
 * 		controller/filePath.
 *
 * If the file is under a specific page, however, it is possible to 
 * create a path relative to the page's directory, instead of the
 * controller's directory:
 *		controller/page/filePath. 
 * thus there is one page argument and two arguments.
 *
 * If the argument list size is 1, it will be interpreted as the
 * file's path relative to the controller's directory.
 *
 * If the argument list size is 2 or greater, the first argument
 * will be interpreted as the page ID, and the remaining arguments
 * will be interpreted as the file's path relative to the page's 
 * directory.
 */
 
$controller = $gyg['page'];
$args = $gyg['args'];
 
if($controller === null || $args === null)
	gyg::throw404();

$argCount = count($args);
if($argCount < 1)
	gyg::throw404();


$page = null;
$file = null;
if($argCount == 1)
{
	$file = $gyg['args'][0];
}
else //if($argCount > 1)
{
	/*
	 * Check if the first arg is the page ID
	 * by checking if the path to it exists.
	 * If not, the arg must be a part of
	 * the path to the file relative to the
	 * controller's directory.
	 */
	$page = "pages/{$gyg['args'][0]}/";
	if(!file_exists(ROOT . "controllers/{$controller}/{$page}"))
	{
		$page = null;
		$file = implode('/', $args);
	}
	else
	{
		$args = array_slice($args, 1);
		$file = implode('/', $args);
	}
}

// controllers/controller/ filepath
// controllers/controller/ pages/page/ filepath
$filePath = ROOT . "controllers/{$controller}/{$page}{$file}";
$imgInfo = getimagesize($filePath);
$mime = $imgInfo['mime'];
header('Content-type: ' . $mime);  
readfile($filePath);
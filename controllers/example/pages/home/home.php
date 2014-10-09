<?php 
$gyg['title'] = "Home";
$gyg['lang'] = 'en';
$gyg['favicon'] = gyg::createFileUrl("img/gyg.png", $gyg['controller']);

// Page template
$gyg['templatePath'] = __DIR__ . "/home.tpl.php";

// Controller stylesheet
$gyg['stylesheet'] = gyg::createFileUrl("style/style.css", $gyg['controller']);

// Link to a controller image and a page image.
// Link to a controller page.
$gyg['content'] = 
"
	<img src = ".gyg::createFileUrl("img/controller.jpg", $gyg['controller']).">
	<img src = ".gyg::createFileUrl("img/page.jpg", $gyg['controller'], $gyg['page']).">
	<a href = '?mars'>Go to mars.</a>
";
				


// Render the page's template.
include(GYG_RENDER_PATH);
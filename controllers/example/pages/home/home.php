<?php

$gyg['title'] = "Home";
$gyg['lang'] = 'en';
$gyg['favicon'] = gyg::createFileUrl("img/gyg.png", $gyg['controller']);

// Page template
$gyg['templatePath'] = __DIR__ . "/home.tpl.php";

// Controller stylesheet
$gyg['stylesheet'] = gyg::createFileUrl("style/style.css", $gyg['controller']);

// Link to home-rewrite
$gyg['content'] = "<p><a href='". gyg::url("example/home-rewrite") . "'>RewriteRule example.</a></p>";

// Link to home-query
$gyg['content'] .= "<p><a href='". gyg::url("example/home-query") . "'>Query string example</a></p>";


// Render the page's template.
include(GYG_RENDER_PATH);
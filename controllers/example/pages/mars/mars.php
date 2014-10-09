<?php 
$gyg['title'] = "Hello Mars!";
$gyg['lang'] = 'en';
$gyg['favicon'] = gyg::createFileUrl("img/gyg.png", $gyg['controller']);

// Page template
$gyg['templatePath'] = __DIR__ . "/mars.tpl.php";

// Controller stylesheet
$gyg['style'] = "@import url(".gyg::createFileUrl("style/style.css", $gyg['controller']).");";

// Page stylesheet
$gyg['stylesheet'] = gyg::createFileUrl("style/style.css", $gyg['controller'], $gyg['page']);


$gyg['header'] = "<img src =".gyg::createFileUrl("img/mars.jpg", $gyg['controller'], $gyg['page']).">";
$gyg['content'] = "<p>Hello Mars!</p>";




include(GYG_RENDER_PATH);
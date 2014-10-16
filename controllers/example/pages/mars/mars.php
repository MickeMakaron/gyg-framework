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
<?php
/*
    Public-Storm
    Copyright (C) 2008-2012 Mathieu Lory <mathieu@internetcollaboratif.info>
    This file is part of Public-Storm.

    Public-Storm is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Public-Storm is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Public-Storm. If not, see <http://www.gnu.org/licenses/>.
 */

$sPlug = new Settings::$VIEWER_TYPE;


$uri = explode('/', $_SERVER['REQUEST_URI']);
#$id = array_pop($uri); # TODO : ca retourne rien ???!!!!

if( Settings::getVar('BASE_URL') != "" )
{
	$ind = 2;
}
else
{
	$ind = 1;
}

header("Content-Type: text/xml; charset: utf-8", false, 200);
$sPlug->AddData("base_url", Settings::getVar('BASE_URL'));
$sPlug->AddData("base_url_http", Settings::getVar('base_url_http'));
$sPlug->AddData("storms", public_storm::getStormsByAlpha());
$sPlug->AddData("users", users::getUsersList());
print $sPlug->fetch("sitemap.tpl", "plugins/sitemap");
exit;


?>
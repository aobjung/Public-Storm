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
    
    Project started on 2008-11-22 with help from Serg Podtynnyi
    <shtirlic@users.sourceforge.net>
 */
 

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

if ( !preg_match('/feedburner/i', $_SERVER['HTTP_USER_AGENT']) && Settings::getVar('feedburner_url') != "" ) 
{
	header("HTTP/1.1 301 moved Permanently", true, 301);
	header("Location: ".Settings::getVar('feedburner_url'), true, 301);
	exit;
}

header("Content-type: application/rss+xml", true, 200);
$sPlug = new Settings::$VIEWER_TYPE;

$sPlug->AddData("base_url_http", Settings::getVar('base_url_http'));
$sPlug->AddData("site_baseline", Settings::getVar('SITE_BASELINE'));
$sPlug->AddData("site_description", strip_tags(i18n::_('description', array(""))));
$sPlug->AddData("site_theme", Settings::getVar('theme_dir'));
$sPlug->AddData("theme_dir_http", Settings::getVar('theme_dir_http'));
$sPlug->AddData("rss_generator", Settings::getVar('RSS_GENERATOR'));
$sPlug->AddData("rss_webmaster", Settings::getVar('RSS_WEBMASTER'));
$sPlug->AddData("rss_managingeditor", Settings::getVar('RSS_MANAGINGEDITOR'));
$sPlug->AddData("version", Settings::getVar('SITE_VERSION'));
$sPlug->AddData("date", date('r'));
#$sPlug->->AddData("i18n", i18n::getLng());

if ( $uri[$ind+1] == "storm" && $id = public_storm::getStormIdFromUrl(urldecode($uri[$ind+2])) ) {
	/* Rss for a storm : list last suggestions */
	$sPlug->AddData("title", i18n::_("Suggestions de '%s'", array(urldecode($uri[$ind+2]))));
	$sPlug->AddData("storm", urldecode($uri[$ind+2]));
	$su = public_storm::getSuggestions($id, Settings::getVar('backend number of items'));
	$sPlug->AddData("suggestions", $su);
	$sPlug->Show("rss_storm.tpl", "plugins/backend");
	//$content = "<pre>".htmlentities($sPlug->fetch("rss.tpl", "plugins/backend"))."</pre>";
} else {
	$sPlug->AddData("title", Settings::getVar('SITE_NAME'));
	$sPlug->AddData("storms", public_storm::getStormsByDate(0, Settings::getVar('backend number of items')));
	$sPlug->Show("rss.tpl", "plugins/backend");
	//$content = "<pre>".htmlentities($sPlug->fetch("rss.tpl", "plugins/backend"))."</pre>";
}
exit;

?>
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

if ( $uri[$ind] )
{
	if ( $lang = i18n::setLocale($uri[$ind]) )
	{
		setcookie("locale", $_SESSION["LANG"], time() + Settings::getVar("languages cookie lifetime"), Settings::getVar("BASE_URL")."/");
		if ( User::isLogged() ) print User::setLang($lang);
		$_SESSION["message"] = i18n::_("Changement de langue effectué");
		//print "<br />".$uri[$ind].$_SESSION["message"]; exit;
	}

	//print $_SERVER['HTTP_REFERER'];
	if ( isset($_SERVER['HTTP_REFERER']) ) {
		header("HTTP/1.1 301 moved Permanently", true, 301);
		header("Location: ".$_SERVER['HTTP_REFERER'], true, 301);
	} else {
		$_SESSION["message"] = "";
		header("HTTP/1.1 301 moved Permanently", true, 301);
		header("Location: ".Settings::getVar('BASE_URL_HTTP'), true, 301);
	}
}


function just_for_having_translations_in_po_file() {
	_("fr_FR.utf8");
	_("en_EN.utf8");
	_("en_GB.utf8");
}

?>
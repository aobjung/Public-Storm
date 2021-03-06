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
		
final class jquery_ui extends Plugins
{
 	public static $subdirs = array();
 	public static $name = "jquery_ui";
	public static $s;
 	
	public function __construct()
	{
		//require(Settings::getVar('prefix') . 'conf/'.self::$name.'.php');
		//require_once('./plugins/'.self::$name.'/classes/'.self::$name.'.php');
		Settings::addCss('screen', rtrim(Settings::getVar('ROOT'), "/").'/plugins/'.self::$name.'/css/custom-theme/jquery-ui-1.8.9.custom.css', 'screen.css');
		Settings::addJs('text/javascript', rtrim(Settings::getVar('ROOT'), "/").'/plugins/'.self::$name.'/js/jquery-1.4.4.min.js', 'jquery.js');
		Settings::addJs('text/javascript', rtrim(Settings::getVar('ROOT'), "/").'/plugins/'.self::$name.'/js/jquery-ui-1.8.9.custom.min.js', 'jquery.js');
		Settings::addJs('text/javascript', rtrim(Settings::getVar('ROOT'), "/").'/plugins/'.self::$name.'/js/jquery.js', 'jquery.js');
		Settings::addJs('text/javascript', rtrim(Settings::getVar('ROOT'), "/").'/plugins/'.self::$name.'/js/jquery_ui.js', 'jquery.js');
	}
	
	public function loadLang()
	{
		return parent::loadLang(self::$name);
	}	
	
	public function getVersion()
	{
		return parent::getVersion();
	}
	
	public function getName()
	{
		return self::$name;
	}
	
	public function getDescription()
	{
		return parent::getDescription();
	}
	
	public function getAuthor()
	{
		return parent::getAuthor();
	}
	
	public function getIcon()
	{
		return parent::getIcon(self::$name);
	}
	
	public function getStatus()
	{
		return parent::getStatus(self::$name);
	}
	
	public function getSubDirs()
	{
		return self::$subdirs;
	}
}


?>

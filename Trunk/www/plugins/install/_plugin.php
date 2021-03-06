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

final class install extends Plugins
{
 	public static $subdirs = array('install');
 	public static $name = "install";
 	
	public function __construct()
	{
		//print "version ".self::$version;
	}
	
	public function buildDbFile($file, $datas)
	{
		$f = new file("./_global_db.php5");
		if ( $f->IsWritable() ) {
			$sContent = '<?php
/* installation is done */
define(\'DB_TYPE\', \''.$datas['type'].'\'); /* mysql exclusively! */
define(\'DB_HOST\', \''.$datas['host'].'\');
define(\'DB_NAME\', \''.$datas['database'].'\');
define(\'DB_USER\', \''.$datas['user'].'\');
define(\'DB_PASS\', \''.$datas['password'].'\');
define(\'DB_PREFIX\', \''.$datas['password'].'\');
?>';
			$f->Write($sContent);
		} else {
			echo "Error !!";
		}
		exit;
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
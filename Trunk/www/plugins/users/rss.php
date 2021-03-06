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


header("Content-type: application/rss+xml", true, 200);
$sPlug = new Settings::$VIEWER_TYPE;

$sPlug->AddData("title", Settings::getVar('SITE_NAME'));
$sPlug->AddData("base_url_http", Settings::getVar('base_url_http'));
$sPlug->AddData("site_baseline", Settings::getVar('SITE_BASELINE'));
$sPlug->AddData("site_description", strip_tags(i18n::_('description', array(""))));
$sPlug->AddData("site_theme", Settings::getVar('theme_dir'));
$sPlug->AddData("theme_dir_http", Settings::getVar('theme_dir_http'));
$sPlug->AddData("rss_generator", Settings::getVar('RSS_GENERATOR'));
$sPlug->AddData("rss_webmaster", Settings::getVar('RSS_WEBMASTER'));
$sPlug->AddData("rss_managingeditor", Settings::getVar('RSS_MANAGINGEDITOR'));
$sPlug->AddData("date", date('r'));
#$sPlug->->AddData("i18n", i18n::getLng());

$sPlug->AddData("storms", public_storm::getStormsByAuthor(0, 20, $user_id));

$sPlug->Show("rss.tpl", "plugins/backend");
//$content = "<pre>".htmlentities($sPlug->fetch("rss.tpl", "plugins/backend"))."</pre>";
exit;

?>
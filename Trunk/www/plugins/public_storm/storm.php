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
$sPlug->AddData("theme_dir", Settings::getVar('theme_dir'));

$uri = explode('/', $_SERVER['REQUEST_URI']);
#$id = array_pop($uri); # TODO : ca retourne rien ???!!!!

if( Settings::getVar('BASE_URL') != "" ) {
	$ind = 2;
} else {
	$ind = 1;
}

$id = public_storm::getStormIdFromUrl(strToLower($storm_permaname));
//identica_php::updateStatus('test 1'.$id);
//print $id."<-------->".$_SESSION['id'];
if ( !isset($id) ) {
	/* le Storm n'existe pas */
	//print "le Storm n'existe pas";
	if( $_SESSION['id'] == "" ) {
		/* pas de user loggé */
		//print "pas de user loggé";
		Settings::setVar('title', "Connectez-vous pour créer le storm : ".$storm_permaname);
		Settings::setVar('meta_description', i18n::_("description", array($storm_permaname)));
		$breadcrumb = Settings::getVar('breadcrumb');
		array_push($breadcrumb, array("name" => i18n::_("Storms"), "link" => Settings::getVar('BASE_URL')."/storms/"));
		array_push($breadcrumb, array("name" => $storm_permaname));
		Settings::setVar('breadcrumb', $breadcrumb);
		$sPlug->AddData("base_url", Settings::getVar('BASE_URL'));
		$sPlug->AddData("theme_plug_dir", Settings::getVar('theme_plug_dir'));
		$sPlug->AddData("storm_permaname", $storm_permaname);
		$content = $sPlug->fetch("add_storm_or_loggin.tpl", "plugins/users");
	} else {
		/* user loggé : on créé le Storm */
		$metaData = users::getMetaData("ask_before_create");
		if ( $metaData["meta_value"] == "true" && $_GET["IAgree"] != "true" ) {
			// user wants to be informed before creating a new storm
			$storm_root = $uri[$ind+2] != "" ? $uri[$ind+2] : $storm_permaname;
			$storm_permaname = str_replace("&", "", $storm_permaname);
			$_SESSION["question"] = i18n::_("Voulez vous vraiment créer le storm '%s' ?", array(urldecode($storm_root)));
		} else {
			// create the storm bypassing the user
			//print "user loggé : on créé le Storm";
			$storm_root = $uri[$ind+2] != "" ? $uri[$ind+2] : $storm_permaname;
					
			//print "Root=".$root."<br/>";
			//print "permaname=".$storm_permaname."<br/>";
			//print "storm_root=".$storm_root."<br/>";
			$storm_permaname = str_replace("&", "", $storm_permaname);
			if ( $id = public_storm::addStorm($storm_permaname, time(), urldecode($storm_root), $_SESSION['id']) ) {
				$_SESSION["message"] = i18n::_("Vous venez de créer le storm %s !", array(urldecode($storm_root)));
				if( DEV != true ) {
					//print "identica_php::updateStatus";
					identica_php::updateStatus(i18n::_("Nouveau storm créé : %s %s par %s", array(urldecode($storm_root), public_storm::getUrl($storm_permaname), $_SESSION["prenom"]." ".$_SESSION["nom"])));
					
					/* TODO: to improve to network delay on Storm creation, we need to test the following line instead of the previous one: */
					//aboutcron::addAction(array("identica_php::updateStatus", json_encode(array("string" => i18n::_("Nouveau storm créé : %s %s par %s", array(urldecode($storm_root), public_storm::getUrl($storm_permaname), $_SESSION["prenom"]." ".$_SESSION["nom"])))), time()));				
				}
				else {
					//identica_php::updateStatus(i18n::_("Nouveau storm créé : %s %s par %s", array(urldecode($storm_root), public_storm::getUrl($storm_permaname), $_SESSION["prenom"]." ".$_SESSION["nom"])));
					//print "updateStatus => ".fixEncoding(i18n::_("Nouveau storm créé : %s %s par %s", array(urldecode($storm_root), public_storm::getUrl($storm_permaname), $_SESSION["prenom"]." ".$_SESSION["nom"])));
				}
			} else {
				$_SESSION["message"] = i18n::_("Erreur lors de la création du storm %s", array(urldecode($storm_root)));
			}
			$storm = public_storm::getStorm($id);
			$storm["storm_id"] = $id;
		}
	}
}


if ( isset($id) ) {
	/* le Storm vient d'être créé ou alors il exstait déjà */
	$storm = public_storm::getStorm($id, 100);
	$root = isset($id) ? $storm['root'] : $storm_permaname;
	backend::addRssfeeds(
		array(
			"href"	=> Settings::getVar('base_url').'/backend/storm/'.urldecode($uri[$ind+1]).'/rss.php',
			"rel"	=> "alternate",
			"type"	=> "application/rss+xml",
			"title"	=> i18n::_("Suggestions de '%s'", array($root)),
		)
	);
	
	/* 
	 * Calculs des Google Rich snippets
	 * http://www.google.com/support/webmasters/bin/answer.py?hl=fr&answer=146645#Aggregate_reviews
	*/
	$sPlug->AddData("votes", count($storm['suggestions']));
	$count = is_null(count($storm['suggestions']))?count($storm['suggestions']):1;
	$sPlug->AddData("rating", public_storm::getNbSuggestionsFromUserId($id, $storm["user_id"])*5 / $count);
	/* fin Google Rich snippets */
	
	$cloud = new tagcloud();
	$is_cloud=false;
	if ( is_array($storm) ) {
		foreach ( $storm["suggestions"] AS $suggestion ) {
			/* on recherche tous les autres storms qui ont la même suggestion */
			$storms_list = public_storm::getStormsFromSuggestion($suggestion['suggestion'], $storm["storm_id"]);
			//print "<pre>";
			//print_r( $storms_list);
			//print "</pre>";
			/* pour chaque storm connexe, on recherche ses suggestions */		
			foreach( $storms_list AS $sugg ) {
				$st = public_storm::getStorm($sugg['storm_id']);
				//print $suggestion['suggestion'].":".$st['root']." (id=".$sugg['storm_id'].")<br />";
				//print "<pre>";
				//print_r($st);
				//print "</pre>";
				$is_cloud=true;
				$cloud->addWord($st['root'], 1);
			}
		}
	}
	//print_r($cloud->getWords());
	if ( $is_cloud==true ) $sPlug->AddData("cloud", $cloud->showCloud());
	
	$storm = is_array($storm) ? $storm : array();
	$suggestions = @$storm["suggestions"];
	$meta_keywords = array();
	if ( @is_array($suggestions) ) {
		$storm["suggestions"] = array_slice($suggestions, 0, 5);
		if ( sizeOf($suggestions) > 5 ) {
			$cloud1 = new tagcloud();
			//print_r(array_slice($suggestions, 6, sizeOf($suggestions)-5));
			foreach( array_slice($suggestions, 5, sizeOf($suggestions)) AS $sugg ) {
				//print_r($sugg);
				$cloud1->addWord($sugg['suggestion'], $sugg['nb']);
			}
			$sPlug->AddData("cloud1", $cloud1->showCloud(true));
		}
		foreach( $suggestions as $suggestion ) {
			array_push($meta_keywords, $suggestion['suggestion']);
		}
	}
	$sPlug->AddData("storm", $storm);
	$sPlug->AddData("cache_dir_http", Settings::getVar('cache_dir_http'));
	$sPlug->AddData("base_url", Settings::getVar('BASE_URL'));
	$get_meta_keywords = Settings::getVar('meta_keywords') != "" ? Settings::getVar('meta_keywords') : i18n::_("meta_keywords");
	Settings::setVar('meta_keywords', implode(", ", $meta_keywords).", ".$get_meta_keywords);
	Settings::setVar('meta_description', i18n::_("description", array($root)));
	#$sPlug->AddData("i18n", i18n::getLng());
	
	//exit;
	
	if( $statuses['graphviz'] == 1 ) {
		/* génération du .dot */
		$dot = "digraph G {
			node [shape=circle, overlap=true];
			edge [len=1];";
		foreach($storm['suggestions'] as $suggestion) {
			$dot .= "\"".ucFirst($storm['root'])."\" -> \"".ucFirst($suggestion['suggestion'])."\" [label=\"".$suggestion['nb']."\"];"; 
		}
		$dot .= "\"".ucFirst($storm['root'])."\" [shape=doublecircle]";
		$dot .= "}";
		/* fin génération du .dot */
	
		$type = Settings::getVar('graphviz_type');
		$file = $storm["storm_id"];
		if($fp = fopen(Settings::getVar('cache_dir') . $file . '.dot', "w+")) {
			fputs($fp, $dot);
			fclose($fp);
			graphviz::renderDotFile(Settings::getVar('cache_dir') . $file . '.dot', Settings::getVar('cache_dir') . $file . '.jpg', 'jpg', Settings::getVar('graphviz_type'));
			/*exec("neato -T$type -Odot " . Settings::getVar('cache_dir') . $file . ".dot");*/
		}
	}
	
	if( $statuses['viadeo_api'] == 1 ) {
		$hubs = viadeo_api::getJsonGroups($root, 5);
		//print_r($hubs["data"]);
		$n=0;
		foreach($hubs["data"] as $data) {
			//print_r($data);
			$containerId = substr($data["link"], strpos($data["link"], "containerId=")+12, strlen($data["link"])); 
			$hubs["data"][$n]["link"] = "http://www.viadeo.com/hu03/".$containerId."/".urlencode($data["name"]);
			$n++;
		}
		if( is_array($hubs) ) $sPlug->AddData("hubs", $hubs["data"]);
	}
	
	Settings::setVar('title', "Storm ".$root);
	$sPlug->AddData("rss_storm", Settings::getVar('base_url').'/backend/storm/'.urldecode($uri[$ind+1]).'/rss.php');
	$breadcrumb = Settings::getVar('breadcrumb');
	array_push($breadcrumb, array("name" => i18n::_("Storms"), "link" => Settings::getVar('BASE_URL')."/storms/"));
	array_push($breadcrumb, array("name" => $root));
	Settings::setVar('breadcrumb', $breadcrumb);
	
	$author = public_storm::getStormAuthor(@$storm['user_id']);
	$sPlug->AddData("username", $author['prenom']." ".$author['nom']);
	$sPlug->AddData("avatar", "http://www.gravatar.com/avatar/".md5( strtolower( $author['email'] ) )."?default=".urlencode( Settings::getVar('base_url_http').Settings::getVar('theme_dir')."/img/weather-storm.png" )."&amp;size=30");
	
	$sPlug->AddData("contributors", public_storm::getContributors($id, "")); //no filter
	$sPlug->AddData("is_favorites", users::isFavorites($id));
	if( $statuses['users'] == 1 ) {
		$user = Array(
			'logged'	=> User::isLogged() != NULL ? 1 : 0,
			'id'		=> $_SESSION['user_id'],
			'prenom'	=> $_SESSION['prenom'],
			'nom'		=> $_SESSION['nom'],
			'email'		=> $_SESSION['email'],
			'avatar'	=> $_SESSION['avatar'],
			'isadmin'	=> $_SESSION['isadmin']
		);
		$sPlug->AddData("user", $user);
	}
	$sPlug->AddData("statuses", $statuses);
	$content = $sPlug->fetch("storm.tpl", "plugins/public_storm");
}





// Fixes the encoding to uf8
function fixEncoding($in_str) {
	$cur_encoding = mb_detect_encoding($in_str) ;
	if($cur_encoding == "UTF-8" && mb_check_encoding($in_str,"UTF-8"))
		return $in_str;
	else
		return utf8_encode($in_str);
} // fixEncoding 


?>

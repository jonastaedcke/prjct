<?php
	//error_reporting(-1);
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);

	include_once('assets/library/Parsedown.php');
	include_once('assets/library/Prjct.php');

	$projects = new Prjct('projects/');
	$info = $projects->get_infoData();

	// https://github.com/erusev/parsedown/wiki/Tutorial:-Get-Started
	$Parsedown = new Parsedown();
	$Parsedown->setBreaksEnabled(true);
	$Parsedown->setMarkupEscaped(true);

	$info_websitetitle = str_replace("\n", "", $Parsedown->text($info["websitetitle"]));
	$info_headerButton = str_replace("\n", "", $Parsedown->text($info["headerButton"]));
	$info_title = str_replace("\n", "", $Parsedown->text($info["title"]));
	$info_name = str_replace("\n", '', $Parsedown->text($info["name"]));
	$info_description = str_replace("\n", '', $Parsedown->text($info["description"]));

	//print_r($projects->get_projectData());
?>
<!DOCTYPE html><html><head><title><?php echo strip_tags($info_websitetitle) ?> - <?php echo $info["name"] ?></title><meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0" name="viewport"><meta content="black-translucent" name="apple-mobile-web-app-status-bar-style"><meta content="yes" name="apple-mobile-web-app-capable"><link href="/assets/css/screen.css" rel="stylesheet" type="text/css"><link href="/assets/image/apple-touch-icon-precomposed.jpg" rel="apple-touch-icon-precomposed"><link href="/#imprint" rel="copyright" title="Copyrights"><meta content="<?php echo $info_name ?>" name="author"><meta content="<?php echo $info_name ?>, prjct, project, work, bio, experience, job" name="keywords"><meta content="<?php echo $info_description ?>" name="description"><meta content="<?php echo $info_title ?>" property="og:title"><meta content="<?php echo $info_websitetitle ?>" property="og:site_name"><meta content="<?php echo $info_description ?>" property="og:description"><meta content="/assets/image/facebookprjct.jpg" property="og:image"><meta content="summary" name="twitter:card"><meta content="@prjctsdotwork" name="twitter:site"><meta content="<?php echo $info_title ?>" name="twitter:title"><meta content="<?php echo $info_description ?>" name="twitter:description"><meta content="/assets/image/twitterprjct.jpg" name="twitter:image"><meta name="google" value="notranslate"></head><body><div id="bar"><div class="space"></div><div class="box"><a href="/"><?php echo $info_websitetitle ?></a></div><div class="space"><div id="contact"><a href="mailto:<?php echo $info["email"] ?>?subject=<?php echo htmlentities("I wanted to tell you something...") ?>&body=<?php echo htmlentities("... and write it here.") ?>"><?php echo $info_headerButton ?></a></div></div></div><div class="topspace"></div><?php
	if(!$projects->get_single()){
?><div id="profile"><div class="box"><div class="profile"><div class="header"><div class="profileimage"></div><div class="cover"><div class="title markdown"><?php echo $info_title ?></div></div></div><div class="name"><div class="value markdown"><?php echo $info_name ?></div></div><div class="desc"><div class="value markdown"><?php echo $info_description ?></div></div><div class="links markdown"><?php echo str_replace("\n", '', $Parsedown->text($info["links"])) ?></div></div></div></div><?php
	}
?><div id="projects"><div class="box"><?php
	foreach($projects->get_projectData() as $project){

		foreach($project["parts"] as $part){
?><div class="<?php echo $projects->get_single() ? "project subproject" : "project" ?>"><div class="images"><?php
	foreach($part["img"] as $img){
?><img alt="Project: <?php echo $project["name"] ?>" src="../<?php echo $img; ?>"><?php
	}
?></div><div class="info"><div class="more"><div class="url"><a href="<?php echo "/".$project["url"] ?>/"><?php echo "/".$project["url"] ?>/</a></div></div><div class="desc markdown"><?php echo str_replace("\n", '', $Parsedown->text($part["desc"])) ?></div><?php
	if($projects->get_single() && count($part["attach"]) > 0){
?><ul class="attachs"><?php
	foreach($part["attach"] as $attach){
?><li><a href="/<?php echo $attach ?>"><?php echo basename($attach) ?></a></li><?php
	}
?></ul><?php
	}
?></div></div><?php
		}
	}
	if($projects->get_single() && count($projects->get_projectData()) === 0){
?><div class="error">There isn't a project. Please go <a href="/">back</a>.</div><?php
	}
?></div></div><div id="footer"><a href="#" name="imprint"></a><div class="box"><?php echo str_replace("\n", '', $Parsedown->text($info["footer"])) ?></div><div class="box"><a href="http://prjct.work" id="prct" target="_blank"></a></div></div><script src="/assets/js/library/jquery-2.1.0.min.js" type="text/javascript"></script><script src="/assets/js/default-min.js" type="text/javascript"></script></body></html>
<?php

chdir("src");

$config = json_decode(file_get_contents("config.json"), true);

if (!$config) die("ERROR: config expected to be valid JSON at src/config.json\n");

echo "Config OK\n";

$header = file_get_contents($config['header']) OR die("header expected at {$config['header']}, but it didn't exist\n");
$footer = file_get_contents($config['footer']) OR die("footer expected at {$config['footer']}, but it didn't exist\n");

function makeMenu($activePage) {
	global $config;
	$s = array();
	foreach($config['pages'] as $pageName => $pageLocation) {
		$pre  = $pageName === $activePage ? "<strong>" : "";
		$post = $pageName === $activePage ? "</strong>" : "";
		$s[] = "<a href=\"$pageLocation\">$pre$pageName$post</a>";
	}
	return implode($s, "&nbsp;&middot;&nbsp;");
}

function makeHeader($activePage) {
	global $config, $header;
	return str_replace("##MENU##",makeMenu($activePage),$header)."<h2>$activePage</h2>";
}

$pages = array();
foreach($config['pages'] as $pageName => &$pageLocation) {
	echo "Loading $pageName...\n";
	$pages[$pageName] = file_get_contents($pageLocation) OR die("ERROR: config defined page $pageName at $pageLocation, but it didn't exist\n");
	$pageLocation = $pageName === $config['home'] ? 'index.html' : str_replace('src','html',$pageLocation);
}

foreach($config['pages'] as $pageName => &$pageLocation) 
	file_put_contents("../out/$pageLocation",makeHeader($pageName).$pages[$pageName].$footer);


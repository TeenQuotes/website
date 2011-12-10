<?php
$page_include=htmlspecialchars($_GET['page']);
if(empty($page_include))
	{
	$page_include="index";
	}
	
if (!file_exists('../'.$page_include.'.php'))
	{
	$page_include = "error";
	}

require '../'.$page_include.'.php';
?>
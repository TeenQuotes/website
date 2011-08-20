<?php

$page_include=htmlspecialchars($_GET['page']);
if(empty($page_include))
	{
	$page_include="index";
	}

require '../'.$page_include.'.php';

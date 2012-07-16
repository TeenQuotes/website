<?php
session_start();
header("Access-Control-Allow-Origin: *");
require "../kernel/config.php";

if (preg_match('/'.$domaine_fr.'/', $_SERVER['SERVER_NAME']) OR preg_match('/'.$domaine_en.'/', $_SERVER['SERVER_NAME']) AND $_SESSION['logged'] == TRUE) 
{
	if (isset($_SESSION['profile_not_fullfilled']) AND $_SESSION['profile_not_fullfilled'] == TRUE)
	{
		unset($_SESSION['profile_not_fullfilled']);
	}
}
<?php
session_start();
header("Access-Control-Allow-Origin: *");
require "../kernel/config.php";
require "../kernel/fonctions.php";

if (isDomainValidForAjax() AND $_SESSION['logged'] == true) 
{
	if (isset($_SESSION['profile_not_fullfilled']) AND $_SESSION['profile_not_fullfilled'] == true)
	{
		unset($_SESSION['profile_not_fullfilled']);
	}
}
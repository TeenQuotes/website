<?php
session_start();
header("Access-Control-Allow-Origin: *");
require "../kernel/config.php";
require "../kernel/fonctions.php";

if (isDomainValidForAjax() AND $_SESSION['logged'] == TRUE) 
{
	if (isset($_SESSION['profile_not_fullfilled']) AND $_SESSION['profile_not_fullfilled'] == TRUE)
	{
		unset($_SESSION['profile_not_fullfilled']);
	}
}
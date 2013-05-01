<?php
// This page is just used for the keyword shortcut "My profile"
session_start();
if ($_SESSION['logged'] AND !empty($_SESSION['id']))
{
	$id = $_SESSION['id'];
	header('Location:../user-'.$id.'');
}
else
	header('Location:../');
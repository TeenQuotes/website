<?php 

include 'header.php';

$errors = addMember("maxime050", "maxisme@navissal.com", "yoyoyo", "");

if(count($errors) == 0){
	echo "Welcome!";
}else{
	echo "Errors :<br/><ul>";
	foreach($errors as $e){
		echo "<li>".$e."</li>";
	}
	echo "</ul>";
}
<?php
include "header.php";
include 'lang/'.$language.'/error.php'; 
$error_code = mysql_real_escape_string($_GET['erreur']);

if ($error_code == '404' OR empty($error_code)) 
{
	echo '
	<div class="post">
		<h1><img src="http://teen-quotes.com/images/icones/erreur.png" class="icone" />'.$error.'</h1>
		<br />
		<br />
		'.$texte_error_404.'
	</div>';
}
elseif ($error_code == '403')
{
	echo '
	<div class="post">
		<h1><img src="http://teen-quotes.com/images/icones/erreur.png" class="icone" />'.$error.'</h1>
		'.$texte_error_403.'
	</div>';
}
elseif ($error_code == '500')
{
	echo '
	<div class="post">
		<h1><img src="http://teen-quotes.com/images/icones/erreur.png" class="icone" />'.$error.'</h1>
		'.$texte_error_500.'
	</div>';
}

include'footer.php';
?>
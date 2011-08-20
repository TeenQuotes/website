<?php 
include "header.php";
include "lang/$language/error.php"; 
$error = mysql_real_escape_string($_GET['erreur']);
// 404
if ($error=="404") {
/*
$domaine = $_SERVER['HTTP_HOST'];
$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
$SCRIPT_URI = $_SERVER['REQUEST_URI'];
$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];

$destinataire = "antoine.augusti@gmail.com";
$sujet = "ERREUR 404 - ".$domaine." - ".$SCRIPT_URI."";

if(empty($HTTP_REFERER))
	{ 
	$provenance = "Pas de lien intermédiaire, connexion directe";
	} 
	else
	{ 
	$provenance = $HTTP_REFERER; 
	}
$message="<br />Une erreur 404 s'est produite sur le site ".$domaine.".<br /><br />
Provenance : ".$provenance."<br />
Page : <b>".$SCRIPT_URI."</b><br />
Navigateur : ".$HTTP_USER_AGENT."<br />
Adresse IP : ".$REMOTE_ADDR."<br />
Nom de domaine : ".gethostbyaddr($REMOTE_ADDR)."<br /><br />
Username : ".$username." - ".$id."";

$message = $top_mail.$message.$end_mail;
mail($destinataire,$sujet,$message,$headers);*/

 ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/erreur.png" class="icone" /><?php echo $error; ?></h1>
<br />
<br />
<?php echo $texte_error_404; ?>
</div>
<?php }
// 403 
elseif ($error=="403") { ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/erreur.png" class="icone" /><?php echo $error; ?></h1>
<?php echo $texte_error_403; ?>
</div>
<?php }
// 500
elseif ($error=="500") { ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/erreur.png" class="icone" /><?php echo $error; ?></h1>
<?php echo $texte_error_500; ?>
</div>
<?php }
else { ?>
<div class="post">
<h1>Oops ! Error !</h1>
Something is technically wrong, please refresh and if it often happens, contact us !
</div>
<?php }
include'footer.php'; ?>
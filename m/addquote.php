<?php 
include "header.php";
$action=$_GET['action'];
include"lang/$language/addquote.php";
if (empty($action)) { ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/add.png" class="icone" /><?php echo $add_quote; ?></h1>
<?php echo $add_consignes; ?>
<form action="addquote.php?action=add_quote" method="post">
<div class="colonne-gauche"><?php echo $enter_quote; ?></div><div class="colonne-milieu"><textarea name="texte_quote" style="height:80px;width:230px;"></textarea></div> 
<br /><br />
<div class="clear"></div>

		<center><p><input type="submit" value="Okey" class="submit" /></p></center>

</form>
<?php }
elseif ($action=="add_quote") { ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/add.png" class="icone" /><?php echo $add_quote; ?></h1>
<?php
$texte_quote= htmlspecialchars(mysql_escape_string($_POST['texte_quote']));
$date = date("d/m/Y");
$texte_quote=stripslashes($texte_quote);


if (strlen($texte_quote) >= '30') {
								$query = mysql_query("INSERT INTO teen_quotes_quotes (texte,auteur,date,auteur_id,approved) VALUES ('$texte_quote', '$username', '$date', '$id','0')");

																		if ($query) {
																		echo "$succes $add_ok";
																		}
																		else 
																		{
																		echo "<h2>$error</h2> $lien_retour";
																		}
									}
									else 
									{
									echo "<span class=\"erreur\">$too_short</span> $lien_retour";
									}
									
	} ?>
</div>
<?php 
include "footer.php"; ?>
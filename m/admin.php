<?php 
include 'header.php';
$action=$_GET['action'];
if ($_SESSION['security_level'] <'2') { header("Location: error.php?erreur=403"); } elseif (empty($action) && $_SESSION['security_level'] >='2') {?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/add.png" class="icone" />Add a quote</h1>
<form action="admin.php?action=add_quote" method="post">
<div class="colonne-gauche">Enter the Quote</div><div class="colonne-milieu"><textarea name="texte_quote" style="height:60px;width:230px;"></textarea></div> 
<br /><br />
<div class="clear"></div>

		<center><p><input type="submit" value="Okey" class="submit" /></p></center>

</form>
</div>

<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/test.png" class="icone" />Approve Quotes</h1>
<?php 
$query = mysql_query("SELECT * FROM teen_quotes_quotes WHERE approved='0' ORDER BY id ASC");
while ($result=mysql_fetch_array($query)) { ?>
		<div class="post">
		<?php echo $result['texte']; ?><br><br />
		<a href="admin.php?action=rate&id=<?php echo $result['id'];?>&approve=yes&auteur=<?php echo $result['auteur_id']; ?>"><img src="http://www.teen-quotes.com/images/icones/succes.png" class="mini_icone" /></a>
		<a href="admin.php?action=rate&id=<?php echo $result['id'];?>&approve=no&auteur=<?php echo $result['auteur_id']; ?>"><img src="http://www.teen-quotes.com/images/icones/delete.png" class="mini_icone" /></a><span class="right"><?php echo $by; ?> <a href="user-<?php echo $result['auteur_id']; ?>" title="View his profile"><?php echo $result['auteur']; ?></a> <?php echo $on; ?> <?php echo $result['date']; ?></span><br><br />
		</div>
<?php } ?>
</div>




<?php }
elseif ($action=="add_quote") { ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/add.png" class="icone" />Add a quote</h1>
<?php
$texte_quote= htmlspecialchars(mysql_escape_string($_POST['texte_quote']));
$date = date("d/m/Y");
$texte_quote=stripslashes($texte_quote);


if (strlen($texte_quote) >= '30') {
								$query = mysql_query("INSERT INTO teen_quotes_quotes (texte,auteur,date,auteur_id) VALUES ('$texte_quote', '$username', '$date', '$id')");

																		if ($query) {
																		echo "$succes <a href=\"admin.php\">Add anoter one</a>";
																		}
																		else 
																		{
																		echo "<h2>$error</h2> $lien_retour";
																		}
									}
									else 
									{
									echo "<h2>$error : Too short</h2> $lien_retour";
									}
									
 }
elseif ($action=="rate") { ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/test.png" class="icone" />Approve Quotes</h1>
<?php
$id_quote=$_GET['id'];
$approve=$_GET['approve'];
$auteur_id=$_GET['auteur'];

if ($approve=="yes") {
$query_texte_quote=mysql_fetch_array(mysql_query("SELECT texte FROM teen_quotes_quotes WHERE id='$id_quote'"));
$texte_quote=$query_texte_quote['texte'];

$approve_quote= mysql_query("UPDATE teen_quotes_quotes set approved='1' WHERE id='$id_quote'");

$query_email_auteur=mysql_fetch_array(mysql_query("SELECT email,username FROM teen_quotes_account WHERE id='$auteur_id'"));
$email_auteur=$query_email_auteur['email'];
$name_auteur=ucfirst($query_email_auteur['username']);

	if ($approve_quote && !empty($email_auteur)) {
												$message = "$image_newsletter Hello <font color=\"#5C9FC0\"><b>$name_auteur</b></font> !<br><br />Your quote \"$texte_quote\" has been <font color=\"#5C9FC0\"><b>approved</b></font> recently by a member of our team ! Congratulations !<br><br />Your Quote is now visible on our website. You can share it or comment it if you want !<br><br /><br />If you want to see your quote, <a href=\"http://www.teen-quotes.com/quote-$id_quote\" target=\"_blank\">click here</a>.<br><br /><br />Sincerely,<br><b>The Teen Quotes Team</b><br /><br /><br />VERSION FRANCAISE :<br /><br />Bonjour <font color=\"#5C9FC0\"><b>$name_auteur</b></font> !<br><br />Votre citation \"$texte_quote\" a été <font color=\"#5C9FC0\"><b>approuvée</b></font> récemment par un membre de notre équipe ! Congratulations !<br><br />Votre citation est maintenant visible sur Teen Quotes. Vous pouvez dès à présent la partager ou la commenter si vous le souhaitez !<br><br /><br />Si vous voulez voir votre citation, <a href=\"http://www.teen-quotes.com/quote-$id_quote\" target=\"_blank\">cliquez ici</a>.<br><br /><br />Cordialement,<br><b>The Teen Quotes Team</b>";
												$mail = mail($email_auteur, "Quote approved", $message, $headers); 
												echo"$succes The author has been notified successfully !";
												echo "<meta http-equiv=\"refresh\" content=\"1;url=admin\" />";
												}
					}
					else
					{
					$query_texte_quote=mysql_fetch_array(mysql_query("SELECT texte FROM teen_quotes_quotes WHERE id='$id_quote'"));
					$texte_quote=$query_texte_quote['texte'];

					$delete_quote= mysql_query("DELETE FROM teen_quotes_quotes WHERE id='$id_quote'");

					$query_email_auteur=mysql_fetch_array(mysql_query("SELECT email,username FROM teen_quotes_account WHERE id='$auteur_id'"));
					$email_auteur=$query_email_auteur['email'];
					$name_auteur=ucfirst($query_email_auteur['username']);
					
						if ($delete_quote && !empty($email_auteur)) {
												$message = "$image_newsletter Hello <font color=\"#5C9FC0\"><b>$name_auteur</b></font> !<br><br />Your quote \"$texte_quote\" has been <font color=\"#5C9FC0\"><b>rejected</b></font> recently by a member of our team...<br><br />Sincerely,<br><b>The Teen Quotes Team</b><br /><br /><br />VERSION FRANCAISE :<br /><br />Bonjour <font color=\"#5C9FC0\"><b>$name_auteur</b></font> !<br><br />Votre citation \"$texte_quote\" a été <font color=\"#5C9FC0\"><b>rejetée</b></font> récemment par un membre de notre équipe...<br><br />Cordialement,<br><b>The Teen Quotes Team</b>";
												$mail = mail($email_auteur, "Quote approved", $message, $headers); 
												echo"$succes The author has been notified successfully !";
												echo "<meta http-equiv=\"refresh\" content=\"1;url=admin\" />";
												}
												
					}
					
					
 }
elseif ($action=="delete_comment") { ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/delete.png" class="icone" />Delete a comment</h1>
<?php
$id_comment=$_GET['id'];

$donnees=mysql_fetch_array(mysql_query("SELECT auteur, auteur_id, texte FROM teen_quotes_comments WHERE id='$id_comment'"));
$auteur_id=$donnees['auteur_id'];
$name_auteur=ucfirst($donnees['auteur']);
$texte_comment=stripslashes($donnees['texte']);

$donnees2=mysql_fetch_array(mysql_query("SELECT email FROM teen_quotes_account WHERE id='$auteur_id'"));
$email_auteur=$donnees2['email'];

$message = "$image_newsletter Hello <font color=\"#5C9FC0\"><b>$name_auteur</b></font> !<br><br />Your comment \"$texte_comment\" has been <font color=\"#5C9FC0\"><b>deleted</b></font> recently by a member of our team...<br><br /><br />Sincerely,<br><b>The Teen Quotes Team</b><br /><br /><br />VERSION FRANCAISE :<br /><br />Bonjour <font color=\"#5C9FC0\"><b>$name_auteur</b></font> !<br><br />Votre commentaire \"$texte_comment\" a été <font color=\"#5C9FC0\"><b>supprimé</b></font> récemment par un membre de notre équipe...<br><br /><br />Cordialement,<br><b>The Teen Quotes Team</b>";
$mail = mail($email_auteur, "Comment deleted", $message, $headers); 

$delete=mysql_query("DELETE FROM teen_quotes_comments where id='$id_comment'");

if ($delete && $mail) {
						echo"$succes The author has been notified successfully !";
						echo "<meta http-equiv=\"refresh\" content=\"1;url=admin\" />";
					  }			
 } ?>
</div>
<?php 
include "footer.php"; ?>
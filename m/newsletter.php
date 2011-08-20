<?php 
include 'header.php';
include "../lang/$language/newsletter.php";
$action=$_GET['action']; ?>
<div class="post">
<h1><img src="http://www.teen-quotes.com/images/icones/mail.png" class="icone" />Newsletter</h1>
<?php
if (empty($action)) { ?>

<?php echo $texte_newsletter; ?>
<form action="newsletter.php?action=send" method="post">
		<center><p><input type="submit" value="<?php echo $inscription_newsletter; ?>" class="submit" /></p></center>
</form>
<?php }
elseif ($action=="send") { // INSCRIPTION
if (!empty($email) && preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) {
$num_rows=mysql_num_rows(mysql_query("SELECT id FROM newsletter where email='$email'")); 
if ($num_rows=="0") {
					$query=mysql_query("INSERT INTO newsletter (email) VALUES ('$email')");
					if ($query) {
								echo ''.$succes_newsletter.'';
								}
					else {
					echo ''.$error.' '.$lien_retour.'';
					}
					}
					else
					{
					echo ''.$already_subscribe.' '.$lien_retour.'';
					}


				}
				else 
				{
				echo ''.$error.'';
				}
}
elseif ($action=="unsuscribe") { // DESINSCRIPTION
$email=htmlspecialchars($_GET['email']);

if (!empty($email) && preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) {
$num_rows=mysql_num_rows(mysql_query("SELECT id FROM newsletter where email='$email'")); 
if ($num_rows=="1") {
					$query=mysql_query("DELETE FROM newsletter WHERE email='$email'");
					if ($query) {
								echo ''.$succes_unsuscribe.'';
								}
					else {
					echo ''.$error.'';
					}
					}
					else
					{
					echo ''.$not_subscribe.'';
					}


				}
				else 
				{
				echo ''.$error.'';
				}
}			
?>
</div>																
<?php 
include "footer.php"; ?>
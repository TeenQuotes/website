<?php 
include 'header.php'; 
include "../lang/$language/quote.php"; 
$id_quote=mysql_real_escape_string($_GET['id_quote']);
$exist_quote = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE id='$id_quote' AND approved='1'"));

if ($exist_quote=='0') 
	{
	echo '<meta http-equiv="refresh" content="0; url=error.php?erreur=404">';
	}

$nombre_commentaires= mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_comments WHERE id_quote='$id_quote'"));
$commentaires = mysql_query("SELECT * FROM teen_quotes_comments WHERE id_quote='$id_quote' ORDER BY id ASC");
$is_favorite = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_favorite WHERE id_quote='$id_quote' AND id_user='$id'"));
$logged= htmlspecialchars($_SESSION['logged']);
// SI PAS D'ID DONNE
if (empty($id_quote)) 
	{
	echo '
	<div class="post">
	<h1>'.$error.'</h1>
	</div>';
	include 'footer.php'; 
	}
	else 
	{ 
	$result = mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_quotes where id='$id_quote' AND approved='1'"));
	$txt_quote = $result['texte_english'];
	$auteur_id = $result['auteur_id'];
	$auteur = $result['auteur']; 
	$date_quote = $result['date'];   ?>

	<div class="post">
	<?php echo $txt_quote; ?><br><br />
	<a href="quote-<?php echo $result['id']; ?>">#<?php echo $result['id']; ?></a><?php afficher_favori_m($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['account']);date_et_auteur_m($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
	</div>
		
	<div class="post">
	<h2><img src="http://www.teen-quotes.com/images/icones/about.png" class="icone" /><?php echo ucfirst($comments); ?><?php if ($nombre_commentaires >'1'){ echo"<span class=\"right\">$nombre_commentaires $comments</span>";}else{echo"<span class=\"right\">$nombre_commentaires $comment</span>";} ?></h2>
	<?php // AFFICHAGE FORMULAIRE AJOUT SI CONNECTE
	if ($_SESSION['logged']) 
		{ ?>
		<form action="add_comment.php" method="post">
		<input type="hidden" name="id_quote" value="<?php echo $id_quote; ?>" />
		<textarea name="texte" style="width:100%;height:50px" onFocus="javascript:this.value=''"><?php echo $warning_comments; ?></textarea>
		<center><p><input type="submit" value="Okey" class="submit" /></p></center>
		</form>
	<?php }
		  else
		  { ?>
		  <span class="erreur"><?php echo $must_be_log; ?></span><br>
		  <br />
	<?php }
		
		
	if ($nombre_commentaires >='1')
		{ // affichage si seulement il y a des commentaires
		while ($donnees = mysql_fetch_array ($commentaires))
		{ 
		$id_auteur=$donnees['auteur_id'];
		$query_avatar= mysql_fetch_array(mysql_query("SELECT avatar FROM teen_quotes_account where id='$id_auteur'"));
		$avatar=$query_avatar['avatar'];?>
		
		<div class="grey_post">
		<?php echo stripslashes($donnees['texte']); ?><br><br />
		<a href="user-<?php echo $donnees['auteur_id']; ?>" title="<?php echo $view_his_profile; ?>"><img src="http://<?php echo $domaine; ?>/images/avatar/<?php echo $avatar; ?>" class="mini_user_avatar" /></a><?php if ($_SESSION['security_level'] >='2'){?> <a href="admin.php?action=delete_comment&id=<?php echo $donnees['id']; ?>"> <img src="http://<?php echo $domaine; ?>/images/icones/delete.png" class="mini_icone" /></a><?php } ?><span class="right"><?php echo $by; ?> <a href="user-<?php echo $donnees['auteur_id']; ?>" title="<?php echo $view_his_profile; ?>"><?php echo $donnees['auteur']; ?></a> <?php echo $on; ?> <?php echo $donnees['date']; ?></span><br>
		</div>
		<?php }
		echo '</div>';
		}
		// NO COMMENTS
		else 
		{
		?>
		<div class="bandeau_erreur">
		<?php echo $no_comments; ?>
		</div>
		<?php
		}
	echo '</div>';
	}

include 'footer.php'; ?>
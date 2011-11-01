<?php
include 'header.php';
$action=$_GET['action'];
$id_user =$_SESSION['account'];
$result = mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_account where id='$id_user'"));
		
		// SELECTED POUR LE TITRE USER
		switch ($result['title']) {
		case "Mr" : $selected_mr='selected="selected"';
		break;
		case "Mrs" : $selected_mrs='selected="selected"';
		break;
		case "Miss" : $selected_miss='selected="selected"';
		break;
		}
		
		switch ($result['hide_profile']) {
		case "0" : $selected_profile_no = 'selected="selected"';
		break;
		case "1" : $selected_profile_yes = 'selected="selected"';
		break;
		}
include "lang/$language/edit_profile.php";
include "lang/$language/newsletter.php";
include "lang/$language/signup.php";


		if(empty($result['birth_date'])) {$result['birth_date']="";}
		if(empty($result['title'])) {$result['title']="";}
		if(empty($result['about_me'])) {$result['about_me']="";}else{$result['about_me'] = nl2br_to_textarea($result['about_me']);}
		if(empty($result['country'])) {$result['country']="";}
		if(empty($result['city'])) {$result['city']="";}
		


// FORMULAIRE
if (empty($action)) {
?>
<div class="post">
	<h1><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone" /><?php echo $edit_profile; ?></h1>
	<img src="http://www.teen-quotes.com/images/avatar/<?php echo $result['avatar']; ?>" class="user_avatar_editprofile" /></span>
	<br />
	<form action="?action=send" method="post">
	<div class="colonne-gauche"><?php echo $choose_title; ?>
	<br /><br />
	<div class="colonne-gauche"><?php echo $choose_birth; ?></div><div class="colonne-milieu"><input type="text" class="signup" name="birth_date" value="<?php echo $result['birth_date']; ?>" /></div>
	<br /><br />
	<div class="colonne-gauche"><?php echo $choose_country; ?></div><div class="colonne-milieu"><input type="text" class="signup" name="country" value="<?php echo $result['country']; ?>" /></div>
	<br /><br />
	<div class="colonne-gauche"><?php echo $choose_city; ?></div><div class="colonne-milieu"><input type="text" class="signup" name="city" value="<?php echo $result['city']; ?>" /></div>
	<br /><br />
	<div class="colonne-gauche"><?php echo $about_you; ?></div><div class="colonne-milieu"><textarea name="about_me" value="<?php echo $result['about_me']; ?>" style="height:60px;width:190px;"><?php echo $result['about_me']; ?></textarea></div> 
	<br /><br />
	<div class="clear"></div>
	<div class="colonne-gauche"><?php echo $hide_profile; ?>
	<br /><br />
	<center><p><input type="submit" value="Okey" class="submit" /></p></center>
	</form>
</div>

<div class="post">
	<h1><img src="http://www.teen-quotes.com/images/icones/outils.png" class="icone" /><?php echo $settings; ?></h1>
	<form action="?action=settings" method="post">
	<input type="checkbox" name="newsletter" value="1" <?php if ($is_newsletter == '1') echo 'checked="checked"';?> /><?php echo $i_want_newsletter; ?><br>
	<input type="checkbox" name="comments_quote" value="1" <?php if($notification_comment_quote == '1')echo 'checked="checked"'; ?> /><?php echo $i_want_comment_quotes; ?><br>
	<br />
	<center><p><input type="submit" value="Okey" class="submit" /></p></center>
	</form>
</div>

<div class="post">
	<h1><img src="http://www.teen-quotes.com/images/icones/avatar.png" class="icone" /><?php echo $change_avatar; ?></h1>
	<?php echo $change_avatar_rules; ?>
	<br />
	<form method="post" action="?action=avatar" enctype="multipart/form-data">
	<div class="colonne-gauche"><?php echo $select_photo; ?></div><div class="colonne-milieu"><input type="file" name="photo" class="signup" /></div>
	<br /><br />
	<a href="?action=reset_avatar"><?php echo $reset_avatar; ?></a><br>
	<center><p><input type="submit" value="Okey" class="submit" /></p></center>
	</form>
</div>


<div class="post">
	<h1><img src="http://www.teen-quotes.com/images/icones/password.png" class="icone" /><?php echo $change_password; ?></h1>
	<form action="?action=change" method="post">
	<div class="colonne-gauche"><?php echo $new_password; ?></div><div class="colonne-milieu"><input type="password" class="signup" name="pass1" /></div><div class="colonne-droite"><span class="min_info">Minimum 5 <?php echo $characters; ?></span></div>
	<br /><br />
	<div class="colonne-gauche"><?php echo $new_password_repeat; ?></div><div class="colonne-milieu"><input type="password" class="signup" name="pass2" /></div>
	<br /><br />
	<center><p><input type="submit" value="Okey" class="submit" /></p></center>
	</form>
</div>

<?php }
elseif ($action=="send") 
	{
// LE FORMULAIRE A ETE ENVOYE ON LE TRAITE
	echo '
	<div class="post">
	<h1><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone" />'.$edit_profile.'</h1>
	';

	$title = htmlspecialchars(mysql_escape_string($_POST['title']));
	$birth_date = htmlspecialchars(mysql_escape_string($_POST['birth_date']));
	$country = ucfirst(htmlspecialchars(mysql_escape_string($_POST['country'])));
	$city = ucfirst(htmlspecialchars(mysql_escape_string($_POST['city'])));
	$about_me = nl2br(htmlspecialchars($_POST['about_me']));
	$hide_profile = htmlspecialchars(mysql_escape_string($_POST['hide_profile']));
				
				
	if(!empty($title) && !empty($birth_date) && !empty($country) && !empty($city) && !empty($about_me) && !empty($hide_profile)) 
	{
	if ($hide_profile=="No") 
		{
		$hide_profile='0';
		}
	if (strlen($about_me) <= '1000') 
		{
		if(preg_match("#[0-9]{2}[\/][0-9]{2}[\/][0-9]{4}#", $birth_date)) 
			{
			$query = mysql_query("UPDATE teen_quotes_account set title='$title', birth_date='$birth_date', country='$country', city='$city', about_me='$about_me', hide_profile='$hide_profile' WHERE id='$id'");
			if ($query) 
				{
				echo ''.$edit_succes.'';
				}
				else 
				{
				echo '<h2>'.$error.'</h2>'.$lien_retour.'';
				}
		
			}
			else
			{
			echo ''.$wrong_birth_date.' '.$lien_retour.'';
			}
		}
		else
		{
		echo ''.$description_long.'';
		}
	}
	else 
	{
	echo ''.$not_completed.'';
	}				
echo '</div>';

}
elseif ($action=="avatar") 
	{
	echo '
	<div class="post">
	<h1><img src="http://www.teen-quotes.com/images/icones/avatar.png" class="icone" />'.$change_avatar.'</h1>
	';
	$photo= $_FILES['photo']['name'];
	$point=".";
	// Testons si le fichier a bien été envoyé et s'il n'y a pas d'erreur
	if (isset($_FILES['photo']) AND $_FILES['photo']['error'] == 0)
		{
		// Testons si le fichier n'est pas trop gros
		if ($_FILES['photo']['size'] <= 550000)
			{
			// Testons si l'extension est autorisée
			$infosfichier = pathinfo($_FILES['photo']['name']);
			$extension_upload = strtolower($infosfichier['extension']);
			$extensions_autorisees = array('jpg', 'gif', 'png','JPG');
			if (in_array($extension_upload, $extensions_autorisees))
				{		
				unlink("./images/avatar/$id.$extension_upload"); // delete de l'image si celle ci existe
				// On peut valider le fichier et le stocker définitivement
				move_uploaded_file($_FILES['photo']['tmp_name'], './images/avatar/' . $id.$point.$extension_upload);
				// on écrit la requête sql 
				$nom_fichier=$id.$point.$extension_upload;
				$sql = "UPDATE teen_quotes_account SET avatar='$nom_fichier' WHERE id='$id'"; 
				mysql_query($sql) or die('Erreur SQL !'.$sql.'<br>'.mysql_error()); 
				echo ''.$change_avatar_succes.'';
				echo '<meta http-equiv="refresh" content="3;url=user-'.$id.'" />';
				}
			else
				{
				echo '<span class="erreur">'.$bad_extension.'</span>'.$lien_retour.'';
				}
			}
		else
			{
			echo '<span class="erreur">'.$photo_extra_size.'</span>'.$lien_retour.'';
			}
		}
	else
		{
		echo '<span class="erreur">'.$select_a_file.'</span>'.$lien_retour.'';
		}
	
	echo '</div>';
	
	}
elseif ($action=="reset_avatar") 
	{
	// RESET DE L'AVATAR 
	echo '
	<div class="post">
	<h1><img src="http://www.teen-quotes.com/images/icones/avatar.png" class="icone" />'.$change_avatar.'</h1>
	';
	$sql = "UPDATE teen_quotes_account SET avatar='icon50.png' WHERE id='$id'"; 
	mysql_query($sql) or die('Erreur SQL !'.$sql.'<br>'.mysql_error()); 
	echo ''.$change_avatar_succes.'';
	echo '<meta http-equiv="refresh" content="3;url=user-'.$id.'" />';
	
	echo '</div>';
	
	}
elseif ($action=="change") 
	{
	//CHANGEMENT DE MOT DE PASSE
	echo '
	<div class="post">
	<h1><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone" />'.$change_password.'</h1>
	';
	$pass1 = htmlspecialchars(mysql_escape_string($_POST['pass1']));
	$pass2 = htmlspecialchars(mysql_escape_string($_POST['pass2']));
			
	if ($pass1==$pass2) 
		{
		if(strlen($pass1) >= '5')
			{
			$pass = sha1(strtoupper($username).':'.strtoupper($pass1));
			$query = mysql_query ("UPDATE teen_quotes_account SET pass='$pass' WHERE id='$id'") or die ('Erreur : '.mysql_error());
			$message = "$email_message";
			$mail = mail($email, "$email_subject", $message, $headers); 
			if($query && $mail)
				{
				echo "$change_pass_succes";
				echo '<meta http-equiv="refresh" content="3;url=connexion.php?method=get&pseudo='.$username.'&password='.$pass2.'" />';
				}
			else 
				{
				echo "<h2>$error</h2>$lien_retour";
				}
	

			}
		else 
			{
			echo '<span class="erreur">'.$password_short.'</span>'.$lien_retour.'';
			}
		}		
	else 
		{
		echo '<span class="erreur">'.$password_not_same.'</span>'.$lien_retour.'';
		} 
		
	echo '</div>';
	
	}
elseif ($action == "settings")
	{
	echo '
	<div class="post">
	<h1><img src="http://www.teen-quotes.com/images/icones/outils.png" class="icone" />'.$settings.'</h1>
	';
	$comments_quote = htmlspecialchars($_POST['comments_quote']);
	$newsletter = htmlspecialchars($_POST['newsletter']);
	$email = $compte['email'];
	
	// NEWSLETTER
	if ($newsletter == '1')
		{
		if (!empty($email) && preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
			{
			$num_rows=mysql_num_rows(mysql_query("SELECT id FROM newsletter where email='$email'")); 
			if ($num_rows=="0") 
				{
				$code = caracteresAleatoires(5);
				$query=mysql_query("INSERT INTO newsletter (email,code) VALUES ('$email','$code')");
				if ($query) 
					{
					echo ''.$settings_updated.'<br /><br />';
					}
					else 
					{
					echo ''.$error.' '.$lien_retour.'';
					}
				}
				else
				{
				echo ''.$settings_updated.'<br /><br />';
				}
			}
			else 
			{
			echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour.'';
			}
		}
	else 
		{
		if (!empty($email) && preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
			{
			$num_rows=mysql_num_rows(mysql_query("SELECT id FROM newsletter WHERE email='$email'")); 
			if ($num_rows=="1") 
				{
				$query=mysql_query("DELETE FROM newsletter WHERE email='$email'");
				if ($query) 
					{
					echo ''.$settings_updated.'<br /><br />';
					}
					else 
					{
					echo ''.$error.' '.$lien_retour.'';
					}
				}
				else
				{
				echo ''.$settings_updated.'<br /><br />';
				}
			}
			else 
			{
			echo ''.$error.' '.$lien_retour.'';
			}
		}
		
	// COMMENTAIRE SUR LES QUOTES DE L'AUTEUR
	if ($comments_quote == '1')
		{
		$query = mysql_query("UPDATE teen_quotes_account SET notification_comment_quote='1' WHERE id = '$id_user'");
		if ($query)
			{
			echo ''.$settings_updated.'<br /><br />';
			}
		else 
			{
			echo ''.$error.' '.$lien_retour.'';
			}
		}
	else
		{
		$query = mysql_query("UPDATE teen_quotes_account SET notification_comment_quote='0' WHERE id = '$id_user'");
		if ($query)
			{
			echo ''.$settings_updated.'<br /><br />';
			}
		else 
			{
			echo ''.$error.' '.$lien_retour.'';
			}
		}
	echo '</div>';
	}
	
	
include "footer.php"; ?>
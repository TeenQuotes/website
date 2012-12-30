<?php
include 'header.php';
$action = $_GET['action'];
$id_user = $_SESSION['id'];
$result = mysql_fetch_array(mysql_query("SELECT * FROM teen_quotes_account where id = '$id_user'"));
$pass1 = htmlspecialchars(mysql_real_escape_string($_POST['pass1']));
$pass2 = htmlspecialchars(mysql_real_escape_string($_POST['pass2']));

$email_quote_today_num_rows = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_settings WHERE param = 'email_quote_today' AND value = '$email'"));
$is_newsletter = mysql_num_rows(mysql_query("SELECT id FROM newsletter where email = '$email'"));
$notification_comment_quote = $result['notification_comment_quote'];

if ($action == "delete_account")
{
	$code = caracteresAleatoires(10);
}

// SELECTED POUR LE TITRE USER
switch ($result['title']) 
{
	case "Mr" : $selected_mr = 'selected="selected"';
	break;
	case "Mrs" : $selected_mrs = 'selected="selected"';
	break;
	case "Miss" : $selected_miss = 'selected="selected"';
	break;
}
		
switch ($result['hide_profile']) 
{
	case "0" : $selected_profile_no = 'selected="selected"';
	break;
	case "1" : $selected_profile_yes = 'selected="selected"';
	break;
}

include 'lang/'.$language.'/edit_profile.php';
include 'lang/'.$language.'/newsletter.php';
include 'lang/'.$language.'/signup.php';


if(empty($result['birth_date'])) 
{
	$result['birth_date'] = NULL;
}
if(empty($result['title']))
{
	$result['title'] = NULL;
}
if(empty($result['about_me']))
{
	$result['about_me'] = NULL;
}
else
{
	$result['about_me'] = nl2br_to_textarea($result['about_me']);
}
if(empty($result['country']))
{
	$result['country'] = NULL;
}
if(empty($result['city']))
{
	$result['city'] = NULL;
}
	
// FORMULAIRE
if (empty($action)) 
{
	echo '
	<div class="post">
		<h1><img src="http://'.$domaine.'/images/icones/profil.png" class="icone" />'.$edit_profile.'</h1>
		<div class="grey_post">
			<img src="http://'.$domaine.'/images/avatar/'.$result['avatar'].'" class="user_avatar_editprofile" /></span>
			<br/>
			<form action="?action=send" method="post">
				<div class="colonne-gauche">'.$choose_title.'
				<br/><br/>
				<div class="colonne-gauche">'.$choose_birth.'</div><div class="colonne-milieu"><input type="text" class="signup" name="birth_date" value="'.$result['birth_date'].'" /></div>
				<br/><br/>
				<div class="colonne-gauche">'.$choose_country.'</div><div class="colonne-milieu">'; select_country($result['country'],$other_countries,$common_choices); echo '</div>
				<br/><br/>
				<div class="colonne-gauche">'.$choose_city.'</div><div class="colonne-milieu"><input type="text" class="signup" name="city" value="'.$result['city'].'" /></div>
				<br/><br/>
				<div class="colonne-gauche">'.$about_you.'</div><div class="colonne-milieu"><textarea name="about_me" value="'.$result['about_me'].'" style="height:60px;width:190px;">'.$result['about_me'].'</textarea></div> 
				<br/><br/>
				<div class="clear"></div>
				<div class="colonne-gauche">'.$hide_profile.'
				<br/><br/>
				<center><p><input type="submit" value="Okay" class="submit" /></p></center>
			</form>
		</div>
	</div>

	<div class="post">
		<h1><img src="http://'.$domaine.'/images/icones/avatar.png" class="icone" />'.$change_avatar.'</h1>
		<div class="grey_post">
			'.$change_avatar_rules.'
			<br/>
			<form method="post" action="?action=avatar" enctype="multipart/form-data">
				<div class="colonne-gauche">'.$select_photo.'</div><div class="colonne-milieu"><input type="file" name="photo" class="signup" /></div>
				<br/><br/>
				<a href="?action=reset_avatar">'.$reset_avatar.'</a><br/>
				<center><p><input type="submit" value="Okay" class="submit" /></p></center>
			</form>
		</div>
	</div>

	<div class="post">
		<h1><img src="http://'.$domaine.'/images/icones/outils.png" class="icone" />'.$settings.'</h1>
		<div class="grey_post">
			<form action="?action=settings" method="post">
				<input type="checkbox" id="input_newsletter" name="newsletter" value="1"'; if ($is_newsletter == '1') echo 'checked="checked"'; echo ' /><label for="input_newsletter">'.$i_want_newsletter.'</label><br/>
				<input type="checkbox" id="input_email_quote_today" name="email_quote_today" value="1"'; if ($email_quote_today_num_rows == '1') echo 'checked="checked"'; echo ' /><label for="input_email_quote_today">'.$i_want_email_quote_today.'</label><br/>
				<input type="checkbox" id="input_comments_quote" name="comments_quote" value="1"'; if($notification_comment_quote == '1') echo 'checked="checked"'; echo ' /><label for="input_comments_quote">'.$i_want_comment_quotes.'</label><br/>
				<br/>
				<center><p><input type="submit" value="Okay" class="submit" /></p></center>
			</form>
		</div>
	</div>

	<div class="post">
		<h1><img src="http://'.$domaine.'/images/icones/password.png" class="icone" />'.$change_password.'</h1>
		<div class="grey_post">
			<form action="?action=change" method="post">
				<div class="colonne-gauche">'.$new_password.'</div><div class="colonne-milieu"><input type="password" class="signup" name="pass1" /></div><div class="colonne-droite"><span class="min_info">Minimum 5 '.$characters.'</span></div>
				<br/><br/>
				<div class="colonne-gauche">'.$new_password_repeat.'</div><div class="colonne-milieu"><input type="password" class="signup" name="pass2" /></div>
				<br/><br/>
				<center><p><input type="submit" value="Okay" class="submit" /></p></center>
			</form>
		</div>
	</div>
	
	<div class="post">
		<h1><img src="http://'.$domaine.'/images/icones/delete.png" class="icone" />'.$delete_account.'</h1>
		<div class="grey_post">
		'.$txt_delete_account.$confirm_delete_by_email.'
		<br/><br/>
			<form action="?action=delete_account" method="post">
				<center><p><input type="submit" value="'.$i_want_to_delete_my_account.'" class="submit" /></p></center>
			</form>
		</div>
	</div>	
	';
}
elseif ($action == "send") 
{
	$error_form = FALSE;

	echo '
	<div class="post">
	<h1><img src="http://'.$domaine.'/images/icones/profil.png" class="icone" />'.$edit_profile.'</h1>
	';

	$title = htmlspecialchars(mysql_real_escape_string($_POST['title']));
	$birth_date = htmlspecialchars(mysql_real_escape_string($_POST['birth_date']));
	$country = ucfirst(htmlspecialchars(mysql_real_escape_string($_POST['country'])));
	$city = ucfirst(htmlspecialchars(mysql_real_escape_string($_POST['city'])));
	$about_me = nl2br(htmlspecialchars(mysql_real_escape_string($_POST['about_me'])));
	$hide_profile = htmlspecialchars(mysql_real_escape_string($_POST['hide_profile']));
				
				
	if((!empty($title) OR !empty($birth_date) OR !empty($country) OR !empty($city) OR !empty($about_me)) OR !empty($hide_profile))
	{
		if ($hide_profile == "No") 
		{
			$hide_profile = '0';
		}

		if (strlen($about_me) <= 1000) 
		{
			// If the birth date is blank OR the birth date is valid
			if ((!empty($birth_date) AND date_est_valide($birth_date)) OR empty($birth_date)) 
			{
				// Everything is ok
			}
			else
			{
				$error_form = TRUE;
				echo $wrong_birth_date.' '.$lien_retour;
			}
		}
		else
		{
			$error_form = TRUE;
			echo $description_long;
		}

		// Update the profile
		if ($error_form == FALSE)
		{
			$query = mysql_query("UPDATE teen_quotes_account SET title = '$title', birth_date = '$birth_date', country = '$country', city = '$city', about_me = '$about_me', hide_profile = '$hide_profile' WHERE id = '$id'");
		}
		
		if ($query) 
		{
			unset ($_SESSION['profile_not_fullfilled']);
			echo $edit_succes;
		}
		else 
		{
			echo '<h2>'.$error.'</h2>'.$lien_retour;
		}
	}
	else 
	{
		echo $not_completed;
	}				
	echo '</div>';
}
elseif ($action == "avatar") 
{
	echo '
	<div class="post">
	<h1><img src="http://'.$domaine.'/images/icones/avatar.png" class="icone" />'.$change_avatar.'</h1>
	';
	$photo = $_FILES['photo']['name'];
	$point = ".";
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
				if (file_exists("./images/avatar/$id.$extension_upload"))
				{
					unlink("./images/avatar/$id.$extension_upload"); // delete de l'image si celle ci existe
				}
				// On peut valider le fichier et le stocker définitivement
				$move = move_uploaded_file($_FILES['photo']['tmp_name'], './images/avatar/' . $id.$point.$extension_upload);
				
				if ($move)
				{
					// on écrit la requête sql 
					$nom_fichier = $id.$point.$extension_upload;
					$sql = "UPDATE teen_quotes_account SET avatar = '$nom_fichier' WHERE id = '$id'"; 
					mysql_query($sql) or die('Erreur SQL !'.$sql.'<br/>'.mysql_error());
					$_SESSION['avatar'] = $nom_fichier;
					echo $change_avatar_succes;
					echo '<meta http-equiv="refresh" content="3;url=user-'.$id.'" />';
				}
				else
				{
					echo '<span class="erreur">'.$error.'</span>'.$lien_retour;
				}
			}
			else
			{
				echo '<span class="erreur">'.$bad_extension.'</span>'.$lien_retour;
			}
		}
		else
		{
			echo '<span class="erreur">'.$photo_extra_size.'</span>'.$lien_retour;
		}
	}
	else
	{
		echo '<span class="erreur">'.$select_a_file.'</span>'.$lien_retour;
	}
	
	echo '</div>';
	
}
elseif ($action == "reset_avatar") 
{
	// RESET DE L'AVATAR 
	echo '
	<div class="post">
	<h1><img src="http://'.$domaine.'/images/icones/avatar.png" class="icone" />'.$change_avatar.'</h1>
	';
	$sql = "UPDATE teen_quotes_account SET avatar = 'icon50.png' WHERE id = '$id'"; 
	mysql_query($sql) or die('Erreur SQL !'.$sql.'<br/>'.mysql_error());
	$_SESSION['avatar'] = 'icon50.png';
	echo $change_avatar_succes;
	echo '<meta http-equiv="refresh" content="3;url=user-'.$id.'" />';
	
	echo '</div>';
	
}
elseif ($action == "change") 
{
	//CHANGEMENT DE MOT DE PASSE
	echo '
	<div class="post">
	<h1><img src="http://'.$domaine.'/images/icones/profil.png" class="icone" />'.$change_password.'</h1>
	';
			
	if ($pass1 == $pass2) 
	{
		if (strlen($pass1) >= 5)
		{
			$pass = sha1(strtoupper($username).':'.strtoupper($pass1));
			$query = mysql_query("UPDATE teen_quotes_account SET pass = '$pass' WHERE id = '$id'") or die ('Erreur : '.mysql_error());
			$mail = mail($email, $email_subject_change_pass, $email_message_change_pass, $headers); 
			if ($query AND $mail)
			{
				echo $change_pass_succes;
				echo '<meta http-equiv="refresh" content="3;url=connexion.php?method=get&pseudo='.$username.'&password='.$pass.'" />';
			}
			else 
			{
				echo '<h2>'.$error.'</h2>'.$lien_retour;
			}
		}
		else 
		{
			echo '<span class="erreur">'.$password_short.'</span>'.$lien_retour;
		}
	}		
	else 
	{
		echo '<span class="erreur">'.$password_not_same.'</span>'.$lien_retour;
	} 
		
	echo '</div>';
}
elseif ($action == "settings")
{
	echo '
	<div class="post">
	<h1><img src="http://'.$domaine.'/images/icones/outils.png" class="icone" />'.$settings.'</h1>
	';
	$comments_quote = htmlspecialchars($_POST['comments_quote']);
	$newsletter = htmlspecialchars($_POST['newsletter']);
	$email_quote_today = htmlspecialchars($_POST['email_quote_today']);
	$email = $_SESSION['email'];
	
	// NEWSLETTER
	if ($newsletter == '1' AND $is_newsletter == '0')
	{
		if (!empty($email) AND preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
		{
			$code = caracteresAleatoires(5);
			$query=mysql_query("INSERT INTO newsletter (email,code) VALUES ('$email','$code')");
			if ($query) 
			{
				echo $settings_updated;
				$notifications_succes = TRUE;
			}
			else 
			{
				echo $error.' '.$lien_retour;
			}
		}
		else 
		{
			echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour;
		}
	}
	elseif ($newsletter != '1' AND $is_newsletter == '1') 
	{
		if (!empty($email) AND preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
		{
			$query = mysql_query("DELETE FROM newsletter WHERE email = '$email'");
			if ($query) 
			{
				echo $settings_updated;
				$notifications_succes = TRUE;
			}
			else 
			{
				echo $error.' '.$lien_retour;
			}
		}
		else 
		{
			echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour;
		}
	}
		
	// INSCRIPTION CITATIONS TOUS LES JOURS PAR MAIL
	if ($email_quote_today == '1' AND $email_quote_today_num_rows == '0')
	{
		if (!empty($email) AND preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
		{
			$query = mysql_query("INSERT INTO teen_quotes_settings (param,value) VALUES ('email_quote_today','$email')");
			if ($query) 
			{
				if ($notifications_succes != TRUE)
				{
					echo $settings_updated;
					$notifications_succes = TRUE;
				}
			}
			else 
			{
				echo $error.' '.$lien_retour;
			}
		}
		else 
		{
			echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour;
		}
	}
	elseif ($email_quote_today != '1' AND $email_quote_today_num_rows == '1')
	{
		if (!empty($email) AND preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) 
		{
			$query = mysql_query("DELETE FROM teen_quotes_settings WHERE param = 'email_quote_today' AND value = '$email'");
			if ($query) 
			{
				if ($notifications_succes != TRUE)
					{
						echo $settings_updated;
						$notifications_succes = TRUE;
					}
			}
			else 
			{
				echo $error.' '.$lien_retour;
			}
		}
		else 
		{
			echo '<span class="erreur">'.$email_incorrect.'</span>'.$lien_retour;
		}
	}
		
	// COMMENTAIRE SUR LES QUOTES DE L'AUTEUR
	if ($comments_quote == '1')
	{
		$query = mysql_query("UPDATE teen_quotes_account SET notification_comment_quote = '1' WHERE id = '$id_user'");
		if ($query)
		{
			if ($notifications_succes != TRUE)
			{
				echo $settings_updated;
			}
		}
		else 
		{
			echo $error.' '.$lien_retour;
		}
	}
	else
	{
		$query = mysql_query("UPDATE teen_quotes_account SET notification_comment_quote = '0' WHERE id = '$id_user'");
		if ($query)
		{
			if ($notifications_succes != TRUE)
			{
				echo $settings_updated;
			}
		}
		else 
		{
			echo $error.' '.$lien_retour;
		}
	}
	echo '</div>';
}
elseif ($action == "delete_account")
{
	echo '
	<div class="post">
	<h1><img src="http://'.$domaine.'/images/icones/delete.png" class="icone" />'.$delete_account.'</h1>
	';
	
	if (!empty($_SESSION['id']) AND !empty($_SESSION['email']))
	{
		$id_user = $_SESSION['id'];
		
		$query = mysql_query("SELECT id FROM delete_account WHERE id_user = '".$id_user."' AND statut = '0'");
		if (mysql_num_rows($query) == '0')
		{
			$insert = mysql_query("INSERT INTO delete_account (id_user,code) VALUES ('".$id_user."', '".$code."')");
			$mail = mail($_SESSION['email'], $email_subject_delete_account, $email_message_delete_account, $headers);

			if ($insert AND $mail)
			{
				echo $succes.' '.$mail_sent_delete_account;
			}
			else
			{
				echo '<div class="bandeau_erreur">'.$error.'</div> '.$lien_retour;
			}
		}
		else
		{
			echo '<div class="bandeau_erreur">'.$already_exist_delete_account.'</div> '.$lien_retour;
		}
	}
	else
	{
		echo '<div class="bandeau_erreur">'.$error.'</div> '.$lien_retour;
	}
	echo '</div>';
}
elseif ($action == "delete_account_confirm")
{
	echo '
	<div class="post">
	<h1><img src="http://'.$domaine.'/images/icones/delete.png" class="icone" />'.$delete_account.'</h1>
	';
	
	$id_user = mysql_real_escape_string($_GET['id']);
	$code = mysql_real_escape_string($_GET['code']);
	
	if (($_SESSION['id'] == $id_user) AND !empty($code))
	{
		$query = mysql_query("SELECT id FROM delete_account WHERE id_user = '".$id_user."' AND code = '".$code."' AND statut = '0'");
		
		if (mysql_num_rows($query) == '1')
		{
			echo '
			<div class="grey_post" style="width:45%;float:left">
			'.$txt_delete_account_short.'
				<form action="?action=delete_account_valide" method="post">
					<input type="hidden" name="code" value="'.$code.'" />
					'.$write_here_delete.'<br/>
					<input type="text" name="confirm" />
					<center><p><input type="submit" value="'.$i_want_to_delete_my_account.'" class="submit" /></p></center>
				</form>
			</div>
			
			<div class="grey_post" style="width:45%;float:right">
			'.$do_not_delete_account.'
				<form action="?action=delete_account_cancel" method="post">
				<input type="hidden" name="code" value="'.$code.'" />
					<center><p><input type="submit" value="'.$i_dont_want_to_delete_my_account.'" class="submit" /></p></center>
				</form>
			</div>
			<div class="clear"></div>';
		}
		else
		{
			echo '<div class="bandeau_erreur">'.$delete_account_not_exist.'</div> '.$lien_retour;
		}
	}
	else
	{
		echo '<div class="bandeau_erreur">'.$error.'</div> '.$lien_retour;
	}
echo '</div>';
	}
elseif ($action == "delete_account_cancel")
{
	echo '
	<div class="post">
	<h1><img src="http://'.$domaine.'/images/icones/delete.png" class="icone" />'.$delete_account.'</h1>
	';
	
	$code = mysql_real_escape_string($_POST['code']);
	$query = mysql_query("SELECT id FROM delete_account WHERE id_user = '".$_SESSION['id']."' AND code = '".$code."' AND statut = '0'");
	
	if (mysql_num_rows($query) == 1)
	{
		$delete = mysql_query("UPDATE delete_account SET statut = '-1' WHERE id_user = '".$_SESSION['id']."' AND code = '".$code."' AND statut = '0'");
		
		if ($delete)
		{
			echo $succes.''.$account_not_deleted_successfully;
		}
		else
		{
			echo '<div class="bandeau_erreur">'.$error.'</div> '.$lien_retour;
		}
	}
	else
	{
		echo '<div class="bandeau_erreur">'.$delete_account_not_exist.'</div> '.$lien_retour;
	}
	echo '</div>';
}
elseif ($action == "delete_account_valide")
{
	echo '
	<div class="post">
	<h1><img src="http://'.$domaine.'/images/icones/delete.png" class="icone" />'.$delete_account.'</h1>
	';
	
	$code = mysql_real_escape_string($_POST['code']);
	$confirm = mysql_real_escape_string($_POST['confirm']);
	
	if ($confirm == $txt_to_write)
	{
		$query = mysql_query("SELECT id FROM delete_account WHERE id_user = '".$_SESSION['id']."' AND code = '".$code."' AND statut = '0'");
		if (mysql_num_rows($query) == '1')
		{
			if ($domaine == $domain_en)
			{
				$update_quote = mysql_query("UPDATE teen_quotes_quotes SET auteur_id = '1211' AND auteur = 'Unknow' WHERE auteur_id = '".$_SESSION['id']."' AND approved IN ('0','1','2')");	
			}
			elseif ($domaine == $domain_fr)
			{
				$update_quote = mysql_query("UPDATE teen_quotes_quotes SET auteur_id = '35' AND auteur = 'Inconnu' WHERE auteur_id = '".$_SESSION['id']."' AND approved IN ('0','1','2')");
			}
			$delete_comments = mysql_query("DELETE FROM teen_quotes_comments WHERE auteur_id = '".$_SESSION['id']."'");
			$delete_favorites = mysql_query("DELETE FROM teen_quotes_favorite WHERE id_user = '".$_SESSION['id']."'");
			$delete_visitors = mysql_query("DELETE FROM teen_quotes_visitors WHERE id_visitor = '".$_SESSION['id']."'");
			$delete_newsletter = mysql_query("DELETE FROM newsletter WHERE email = '".$_SESSION['email']."'");
			$delete_newsletter_quotidienne = mysql_query("DELETE FROM teen_quotes_settings WHERE param = 'email_quote_today' AND value = '".$_SESSION['email']."'");
			$delete_account = mysql_query("DELETE FROM teen_quotes_account WHERE id = '".$_SESSION['id']."'");
			
			if ($delete_account AND $delete_newsletter_quotidienne AND $delete_visitors AND $delete_favorites AND $delete_comments AND $update_quote)
			{
				$update_statut = mysql_query("UPDATE delete_account SET statut = '1' WHERE code = '".$code."'");
				echo $succes.' '.$account_deleted_successfully;
				echo '<meta http-equiv="refresh" content="5;url=?deconnexion" />';
			}
			else
			{
				echo '<div class="bandeau_erreur">'.$error.'</div> '.$lien_retour;
			}
		}
		else
		{
			echo '<div class="bandeau_erreur">'.$delete_account_not_exist.'</div> '.$lien_retour;
		}
	}
	else
	{
		echo '<div class="bandeau_erreur">'.$wrong_txt_to_write.'</div> '.$lien_retour;
	}
	echo '</div>';
}

include "footer.php";
?>
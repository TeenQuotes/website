<?php
$not_specified = "Non renseigné";
$change_avatar = "Changer d'avatar";
$change_avatar_rules = "
<ul>
	<li>Votre photo sera redimensionnée en 120 pixels de hauteur et de largeur</li>
	<li>Votre photo doit être au format JPG, GIF ou PNG</li>
	<li>La taille de votre photo ne doit pas excéder 500 ko</li>
</ul>";
$select_photo = "Sélectionnez votre photo";
$edit_profile = "Modifier mon profil";
$reset_avatar = "Je veux l'avatar par défaut !";
$choose_title = "<span class=\"bleu\">Titre : </span> 		
				</div><div class=\"colonne-milieu\"><select name=\"title\" style=\"width:197px\"> 
				<option value=\"\">Choisissez</option> 
				<option $selected_mr value=\"Mr\">Monsieur</option> 
				<option $selected_mrs value=\"Mrs\">Madame</option> 
				<option $selected_miss value=\"Miss\">Mademoiselle</option> 
			</select></div>";
$choose_title_m = "<span class=\"bleu\">Titre : </span><br>		
				<select name=\"title\" style=\"width:197px\"> 
				<option value=\"\">Choisissez</option> 
				<option $selected_mr value=\"Mr\">Monsieur</option> 
				<option $selected_mrs value=\"Mrs\">Madame</option> 
				<option $selected_miss value=\"Miss\">Mademoiselle</option> 
			</select>";
$choose_birth = "<span class=\"bleu\">Date de naissance (JJ/MM/AAAA) : </span>";
$choose_country= "<span class=\"bleu\">Pays : </span>";
$other_countries = "Autres pays";
$common_choices = "Choix communs";
$choose_city = "<span class=\"bleu\">Ville : </span>";
$about_you = "<span class=\"bleu\">À propos de vous : </span>";
$hide_profile = "<span class=\"bleu\">Cacher mon profil : </span>
			</div><div class=\"colonne-milieu\"><select name=\"hide_profile\" style=\"width:197px\"> 
				<option $selected_profile_no value=\"No\">Non</option> 
				<option $selected_profile_yes value=\"1\">Oui</option> 
			</select></div>";
$hide_profile_m = "<span class=\"bleu\">Cacher mon profil : </span><br>
			<select name=\"hide_profile\" style=\"width:197px\"> 
				<option $selected_profile_no value=\"No\">Non</option> 
				<option $selected_profile_yes value=\"1\">Oui</option> 
			</select>";

$settings = "Options";			
$i_want_newsletter = "Je veux recevoir la newsletter hebdomadaire";
$i_want_email_quote_today = "Je veux recevoir les nouvelles citations tous les jours";
$i_want_comment_quotes = "Je veux recevoir un email quand un commentaire sera posté sur une de mes citations";
			
			
			
$edit_succes = "$succes Votre profil a été modifié avec succès !<br><br /><br />&raquo; <a href=\"../\">Retour à l'accueil</a><br><br />";
$description_long = "<span class=\"erreur\">Votre description de vous-même est trop longue !</span>$lien_retour";
$not_completed = "<span class=\"erreur\">Vous n'avez pas complété tout le formulaire !</span>$lien_retour";
$wrong_birth_date = "<span class=\"erreur\"> Merci d'entrer une date de naissance valide (JJ/MM/AAAA) !</span>";


$change_password = "Changer mon mot de passe";
$new_password = "Nouveau mot de passe";
$new_password_repeat = "Répétez votre nouveau mot de passe";
$characters = "caractères";

$email_subject_change_pass = "Nouveau mot de passe";
$email_message_change_pass = "$top_mail Bonjour $username !<br><br />Vous venez de changer votre mot de passe sur ".$name_website.".<br><br />Vos nouveaux identifiants sont :<br /><br /><li>Pseudo : <font color=\"#5C9FC0\"><b>$username</b></font></li><li>Mot de passe : <font color=\"#5C9FC0\"><b>$pass2</b></font></li><br /><br />Gardez les précieusement ! Vous pouvez vous connecter dès maintenant en cliquant <a href=\"http://".$domaine."/connexion.php?method=get&pseudo=$username&password=$pass\" target=\"_blank\">sur ce lien</a>.<br><br />Cordialement,<br><b>The ".$name_website." Team</b> $end_mail";
$change_pass_succes = "$succes Votre mot de passe a été changé avec succès !<br><br /><br />Vos identifiants vous ont été envoyés sur votre adresse email.";
$password_short = "Votre mot de passe est trop court.";
$password_not_same = "Les mots de passe ne sont pas identiques.";
$change_avatar_succes = "$succes Votre avatar a été mis à jour avec succès !<br><br /><br />Vous allez être redirigé dans un instant...";


$photo_extra_size = "La taille de votre photo est trop grande ! Le maximum est de 500 ko !";
$bad_extension= "Votre photo doit être au format JPG, GIF ou PNG !";
$select_a_file = "Merci de sélectionner une image !";

$settings_updated = "$succes Vos options ont été mises à jour avec succès !";

$delete_account = "Supprimer mon compte";
$txt_delete_account = "En supprimant votre compte sur ".$name_website.", seront supprimés :<br>
<ul>
	<li>L'intégralité de votre compte</li>
	<li>Vos commentaires</li>
	<li>Vos citations favorites</li>
</ul>
En revanche, si vous avez proposé des citations et qu'elles ont été acceptées, elles ne seront pas supprimées. Elles seront associées à un compte par défaut.<br>";
$confirm_delete_by_email = "<br />
Vous devrez valider la suppression de votre compte par email.";
$i_want_to_delete_my_account = "Je veux supprimer mon compte";
$email_subject_delete_account = "Suppression de votre compte";
$email_message_delete_account = "".$top_mail." Bonjour ".$_SESSION['username'].",<br>
<br />
Vous voulez supprimer votre compte sur ".$name_website.". Pour confimer ce choix, vous devez cliquer sur <a href=\"http://".$domaine."/editprofile?action=delete_account_confirm&id= ".$_SESSION['id']."&code= ".$code."\">ce lien</a>.<br>
<br />
".$txt_delete_account."<br>
".$end_mail."";

$mail_sent_delete_account = "Vous voulez supprimer votre compte sur ".$name_website.". Pour confimer ce choix, vous devez cliquer sur le lien qui a été envoyé à votre adresse email (".$_SESSION['email'].").";
$already_exist_delete_account = "Vous avez déjà demandé à supprimer votre compte.";

$txt_delete_account_short = "En supprimant votre compte sur ".$name_website.", seront supprimés :<br>
<ul>
	<li>L'intégralité de votre compte</li>
	<li>Vos commentaires</li>
	<li>Vos citations favorites</li>
</ul>";
$delete_account_not_exist = "Impossible de trouver une demande avec ces informations";
$txt_to_write = "EFFACER";
$write_here_delete = "Écrivez ici \"".$txt_to_write."\"";
$do_not_delete_account = "J'ai bien réfléchi et je veux garder mon compte sur ".$name_website.".";
$i_dont_want_to_delete_my_account = "Je veux garder mon compte";

$account_not_deleted_successfully = "Votre compte n'a pas été supprimé ! Ouf...";

$account_deleted_successfully = "Votre compte et toutes les informations associées ont été définitivement supprimées. Vous serez déconnecté définitivement dans 5 secondes...";
$wrong_txt_to_write = "Vous n'avez pas entré le bon texte.";
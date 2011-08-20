<?php
$not_specified = "Non renseigné";
$change_avatar ="Changer d'avatar";
$change_avatar_rules="<li>Votre photo sera redimensionnée en 120 pixels de hauteur et de largeur</li>
<li>Votre photo doit être au format JPG, GIF ou PNG</li>
<li>La taille de votre photo ne doit pas excéder 500 ko</li>";
$select_photo="Sélectionnez votre photo";
$edit_profile = "Modifier mon profil";
$reset_avatar="Je veux l'avatar par défaut !";
$choose_title = "<span class=\"bleu\">Titre : </span> 		
				</div><div class=\"colonne-milieu\"><select name=\"title\" style=\"width:197px\"> 
				<option value=\"\">Choisissez</option> 
				<option $selected_mr value=\"Mr\" >Monsieur</option> 
				<option $selected_mrs value=\"Mrs\" >Madame</option> 
				<option $selected_miss value=\"Miss\" >Mademoiselle</option> 
			</select></div>";
$choose_birth = "<span class=\"bleu\">Date de naissance (JJ/MM/AAAA) : </span>";
$choose_country= "<span class=\"bleu\">Pays : </span>";
$choose_city= "<span class=\"bleu\">Ville : </span>";
$about_you = "<span class=\"bleu\">A propos de vous : </span>";
$hide_profile = "<span class=\"bleu\">Cacher mon profil : </span>
			</div><div class=\"colonne-milieu\"><select name=\"hide_profile\" style=\"width:197px\"> 
				<option value=\"No\" >Non</option> 
				<option value=\"1\" >Oui</option> 
			</select></div>";
			
			
			
$edit_succes = "$succes Votre profil a été modifié avec succès !<br><br /><br />&raquo; <a href=\"index.php\">Retour à l'index</a><br><br />";
$description_long = "<span class=\"erreur\">Votre description de vous-même est trop longue !</span>$lien_retour";
$not_completed ="<span class=\"erreur\">Vous n'avez pas complété tout le formulaire !</span>$lien_retour";
$wrong_birth_date = "<span class=\"erreur\"> Merci d'entrer une date de naissance valide (JJ/MM/AAAA) !</span>";


$change_password = "Changer mon mot de passe";
$new_password = "Nouveau mot de passe";
$new_password_repeat = "Répétez votre nouveau mot de passe";
$characters = "charactères";

$email_subject = "Nouveau mot de passe";
$email_message = "$top_mail Bonjour $username !<br><br />Vous venez de changer votre mot de passe sur Teen Quotes<br><br />Vos nouveaux identifiants sont :<br /><br /><li>Pseudo : <font color=\"#5C9FC0\"><b>$username</b></font></li><li>Mot de passe : <font color=\"#5C9FC0\"><b>$pass2</b></font></li><br /><br />Gardez les précieusement ! Vous pouvez vous connecter dès maintenant en cliquant <a href=\"http://www.teen-quotes.com/connexion.php?method=get&pseudo=$username&password=$pass2\" target=\"_blank\">sur ce lien</a>.<br><br />Cordialement,<br><b>The Teen Quotes Team</b> $end_mail";
$change_pass_succes = "$succes Votre mot de passe a été changé avec succès !<br><br /><br />Vos identifiants vous ont été envoyés sur votre adresse email.";
$password_short = "Votre mot de passe est trop court.";
$password_not_same = "Les mots de passe ne sont pas identiques.";
$change_avatar_succes ="$succes Votre avatar a été mis à jour avec succès !<br><br /><br />Vous allez être redirigé dans un instant...";


$photo_extra_size = "La taille de votre photo est trop grande ! Le maximum est de 500 ko !";
$bad_extension="Votre photo doit être au format JPG, GIF ou PNG !";
$select_a_file = "Merci de sélectionner une image !";

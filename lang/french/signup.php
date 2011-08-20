<?php

$account_create = "Pour créer un compte sur Teen Quotes et être en mesure d'accéder à tous les avantages qui vont avec, remplissez le formulaire ci-dessous, votre compte sera créé dans un instant!";
$require_age = "Vous devez avoir 13 ans ou plus pour créer un compte membre sur Teen Quotes";
$username_enter = "Nom d'utilisateur";
$password = "Mot de passe";
$confirm_password = "Confirmation du mot de passe";
$create_account = "Créer mon compte !";
$characters = "charactères";
$reenter_pass = "Entrez à nouveau votre mot de passe";
$valid_email = "Merci d'entrer une adresse valide, elle sera nécessaire plus tard.";

$username=ucfirst(str_replace(' ','',$username));
$email_subject = "Bienvenue";
$email_message = "$top_mail Bienvenue sur Teen Quotes !<br><br />Pendant que vous vous installez tranquillement et que vous découvrez Teen Quotes, nous vous envoyons vos informations pour vous connecter.<br><br />Vos identifiants sont les suivants :<br /><br /><li>Nom d'utilisateur : <font color=\"#5C9FC0\"><b>$username</b></font></li><li>Mot de passe : <font color=\"#5C9FC0\"><b>$pass2</b></font></li><br /><br />Conservez les précieusement !<br><br />Vous pouvez vous connecter dès maintenant en cliquant <a href=\"http://www.teen-quotes.com/connexion.php?method=get&pseudo=$username&password=$pass2\" target=\"_blank\">sur ce lien</a>. Pensez dès que vous le pourrez à remplir votre profil !<br><br />Cordialement,<br><b>The Teen Quotes Team</b> $end_mail";
$signup_succes = "$succes Votre compte a été créé avec succès !<br><br />Bienvenue sur Teen Quotes!<br><br />Pendant que vous vous installez tranquillement et que vous découvrez Teen Quotes, nous vous envoyons vos informations pour vous connecter.<br><br />Pensez dès que vous le pourrez à remplir votre profil !<br><br /><br /><br />Vos identifiants vous ont été envoyés sur votre adresse email ($email). Vous allez être connecté dans quelques secondes...";

$email_taken = "Votre adresse email est déjà utilisée.";
$email_incorrect = "Votre adresse email n'est pas correcte.";
$password_short = "Votre mot de passe est trop court.";
$password_not_same = "Les mots de passe sont différents.";
$username_taken = "Votre nom d'utilisateur est déjà utilisé.";
$username_short = "Votre nom d'utilisateur est trop court.";
$username_not_valid = "Votre nom d'utilisateur ne respecte pas la forme demandée :</span><br>
<br />
<li> Le pseudo ne peut comporter que des miniscules, majuscules et des chiffres</li>
<li> Il doit être de 20 caractères maximum (avec ou sans chiffres)</li>
<li> Aucun espaces ni caractères spéciaux</li>";

$must_be_registered_for_quote = '<div class="erreur_addquote"> Vous devez vous inscrire pour soumettre une citation !</div>';

?>

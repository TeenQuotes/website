<?php
$account_create = "To create an account on ".$name_website." and be able to access all the advantages that come with it, fill in the form below, your account will be created in an instant !";
$require_age = "You must be 13 or older to create a ".$name_website."'s account.";
$username_enter = "Username";
$password = "Password";
$confirm_password = "Confirm password";
$create_account = "Create my account !";
$characters = "letters";
$reenter_pass = "Reenter your password";
$valid_email = "Please enter a valid email address, it will be necessary later";


$username = str_replace(' ','',$username);
$email_subject = "Welcome";
$email_message = "$top_mail Welcome to ".$name_website." !<br><br />While you set up and you discover ".$name_website.", we send you some informations to log.<br><br />Your credentials are :<br /><br /><li>Username : <font color=\"#5C9FC0\"><b>$username</b></font></li><li>Password : <font color=\"#5C9FC0\"><b>$pass2</b></font></li><br /><br />Keep precisely !<br><br />You can login now by clicking on <a href=\"http://".$domaine."/connexion.php?method=get&pseudo=$username&password=$pass\" target=\"_blank\">this link</a>. Remember to fill out your profile as soon as you can !<br><br />Sincerely,<br><b>The ".$name_website." Team</b> $end_mail";
$signup_succes = "$succes Your account has been created with success !<br><br />Welcome to ".$name_website."!<br><br />While you set up and you discover ".$name_website.", we send you some informations to log.<br><br />Think about when you can complete your profile !<br><br /><br /><br />Your login will be sent to your email address ($email). You will be logged in a few seconds...";

$email_taken = "Your email address is already taken.";
$email_incorrect = "Your email address is not correct.";
$password_short = "Your password is too short.";
$password_not_same = "Passwords are not the same.";
$username_shape = "[a-z] [0-9] and _";
$username_taken = "Your username is already taken.";
$username_short = "Your username is too short.";
$username_not_valid = "Your username does not meet the required shape :<br>
<br />
<li> Your nickname can contain only lower case [a-z], numbers [0-9] and an underscore [_].</li>
<li> Your nickname must be between 5 and 20 letters.</li>
<li> No spaces or special characters.</li>";

$must_be_registered_for_quote = '<div class="erreur_addquote"> You must be registered if you want to add your quote !</div>';
$must_be_registered_to_comment = '<div class="erreur_addquote"> You must be registered if you want to add a comment !</div>';
?>
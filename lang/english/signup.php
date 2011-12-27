<?php

$account_create = "To create an account on Teen Quotes and be able to access all the advantages that come with it, fill in the form below, your account will be created in an instant !";
$require_age = "You must be 13 or older to create a Teen Quotes's account.";
$username_enter = "Username";
$password = "Password";
$confirm_password = "Confirm password";
$create_account = "Create my account !";
$characters = "letters";
$reenter_pass = "Reenter your password";
$valid_email = "Please enter a valid email address, it will be necessary later";


$username=ucfirst(str_replace(' ','',$username));
$email_subject = "Welcome";
$email_message = "$top_mail Welcome to Teen Quotes !<br><br />While you set up and you discover Teen Quotes, we send you some informations to log.<br><br />Your credentials are :<br /><br /><li>Username : <font color=\"#5C9FC0\"><b>$username</b></font></li><li>Password : <font color=\"#5C9FC0\"><b>$pass2</b></font></li><br /><br />Keep precisely !<br><br />You can login now by clicking on <a href=\"http://www.teen-quotes.com/connexion.php?method=get&pseudo=$username&password=$pass\" target=\"_blank\">this link</a>. Remember to fill out your profile as soon as you can !<br><br />Sincerely,<br><b>The Teen Quotes Team</b> $end_mail";
$signup_succes = "$succes Your account has been created with success !<br><br />Welcome to Teen Quotes!<br><br />While you set up and you discover Teen Quotes, we send you some informations to log.<br><br />Think about when you can complete your profile !<br><br /><br /><br />Your login will be sent to your email address ($email). You will be logged in a few seconds...";

$email_taken = "Your email adress is already taken.";
$email_incorrect = "Your email adress is not correct.";
$password_short = "Your password is too short.";
$password_not_same = "Passwords are not the same.";
$username_taken = "Your username is already taken.";
$username_short = "Your username is too short.";
$username_not_valid = "Your username does not meet the required shape :</span><br>
<br />
<li> Your nickname can contain only lower case, uppercase and numbers </li>
<li> Your nickname must be less than 20 characters</li>
<li> No spaces or special characters </li> ";

$must_be_registered_for_quote = '<div class="erreur_addquote"> You must be registered if you want to add your quote !</div>';
$must_be_registered_to_comment = '<div class="erreur_addquote"> You must be registered if you want to add a comment !</div>';
?>
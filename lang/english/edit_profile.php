<?php
$not_specified = "Not specified";
$edit_profile = "Edit my profile";
$change_avatar = "Change my picture";
$change_avatar_rules= "
<ul>
	<li>Your photo will be resized to 120 pixels in height and width</li>
	<li>Your photo must be in JPG, PNG or GIF</li>
	<li>Maximum size: 500 ko</li>
</ul>
<img src=\"http://".$domain."/images/icones/infos.png\" class=\"mini_icone\"/>Your avatar will not appear on Google.<br/><br/>";
$select_photo= "Select your photo";
$reset_avatar= "I want the default picture!";
$choose_title = "<span class=\"bleu\">Title: </span> 
			</div><div class=\"colonne-milieu\"><select name=\"title\" style=\"width:197px\"> 
				<option value=\"\">Choose...</option> 
				<option $selected_mr value=\"Mr\">Mr</option> 
				<option $selected_mrs value=\"Mrs\">Mrs</option> 
				<option $selected_miss value=\"Miss\">Miss</option> 
			</select></div>";
$choose_title_m = "<span class=\"bleu\">Title: </span><br/> 
			<select name=\"title\" style=\"width:197px\"> 
				<option value=\"\">Choose...</option> 
				<option $selected_mr value=\"Mr\">Mr</option> 
				<option $selected_mrs value=\"Mrs\">Mrs</option> 
				<option $selected_miss value=\"Miss\">Miss</option> 
			</select>";
$choose_birth = "<span class=\"bleu\">Date of birth (DD/MM/YYYY): </span>";
$choose_country= "<span class=\"bleu\">Country: </span>";
$other_countries = "Other countries";
$common_choices = "Common choices";
$choose_city= "<span class=\"bleu\">City: </span>";
$about_you = "<span class=\"bleu\">About you: </span>";
$hide_profile = "<span class=\"bleu\">Hide my profile: </span>
			</div><div class=\"colonne-milieu\"><select name=\"hide_profile\" style=\"width:197px\"> 
				<option $selected_profile_no value=\"No\">No</option> 
				<option $selected_profile_yes value=\"1\">Yes</option> 
			</select></div>";
$hide_profile_m = "<span class=\"bleu\">Hide my profile: </span><br/>
			<select name=\"hide_profile\" style=\"width:197px\"> 
				<option $selected_profile_no value=\"No\">No</option> 
				<option $selected_profile_yes value=\"1\">Yes</option> 
			</select>";

$settings = "Settings";
$i_want_newsletter = "I want to receive the weekly newsletter";
$i_want_email_quote_today = "I want to receive new quotes everyday";
$i_want_comment_quotes = "I want to receive an email when a comment will be posted on one of my quotes";
			
			
			
$edit_succes = $succes." Your profile has been changed successfully!<br/><br/><br/>&raquo; <a href=\"../\">Back to home</a><br/><br/>";
$description_long = "<span class=\"erreur\">Your description of yourself is too long!</span>$lien_retour";
$not_completed = "<span class=\"erreur\">You have not completed all the form!</span>$lien_retour";
$wrong_birth_date = "<span class=\"erreur\">Please enter a valid birth date (DD/MM/YYYY)!</span>";


$change_password = "Change my password";
$new_password = "New password";
$new_password_repeat = "Repeat your new password";
$characters = "letters";


$email_subject_change_pass = "New password";
$email_message_change_pass = "$top_mail Hello <font color=\"#394DAC\"><b>$username</b></font>!<br/><br/>You have recently change your password on ".$name_website.".<br/><br/>Your new credentials are:<br/><br/><li>Username: <font color=\"#394DAC\"><b>$username</b></font></li><li>Password: <font color=\"#394DAC\"><b>$pass1</b></font></li><br/>Keep precisely! You can login now by clicking on <a href=\"http://".$domain."/connexion.php?method=get&pseudo=$username&password=$pass\" target=\"_blank\">this link</a>. <br/><br/>Sincerely,<br/><b>The ".$name_website." Team</b> $end_mail";
$change_pass_succes = $succes." Your password was changed successfully!<br/><br/><br/>Your login will be sent to your email address.";
$password_short = "Your password is too short.";
$password_not_same = "Passwords are not the same.";

$change_avatar_succes = $succes." Your photo has been updated successfully!<br/><br/><br/>You will be redirected in a moment";

$photo_extra_size = "The size of your photo is too big! The maximum is 500 ko!";
$bad_extension = "Your photo must be in JPG, PNG or GIF!";
$select_a_file = "Please select a file!";
	
$settings_updated = $succes." Your options have been updated successfully!";

$delete_account = "Delete my account";
$txt_delete_account = "By deleting your account on ".$name_website.", will be deleted:<br/>
<ul>
	<li>Your entire account</li>
	<li>Your comments</li>
	<li>Your favorite quotes </li>
</ul>
But if you suggested quotes and they were accepted, they will not be deleted. They will be associated to a default account. <br/>";
$confirm_delete_by_email = "<br/>
You will need to confirm the deletion of your account by email.";
$i_want_to_delete_my_account = "I want to delete my account";
$email_subject_delete_account = "Delete your account";
$email_message_delete_account = "".$top_mail." Hello ".$_SESSION['username'].",<br/>
<br/>
You want to delete your account on ".$name_website.". To confirm this request, you must click on <a href=\"http://".$domain."/editprofile?action=delete_account_confirm&id=".$_SESSION['id']."&code=".$code."\">this link</a>.<br/>
<br/>
".$txt_delete_account."<br/>
".$end_mail."";

$mail_sent_delete_account = "You want to delete your account on ".$name_website.". To confirm this request, you must click on the link that was sent to your email address (".$_SESSION['email'].").";
$already_exist_delete_account = "You have already asked to remove your account.";

$txt_delete_account_short = "By deleting your account on ".$name_website.", will be deleted:<br/>
<ul>
	<li>Your entire account</li>
	<li>Your comments</li>
	<li>Your favorite quotes </li>
</ul>";
$delete_account_not_exist = "Can not find a request with this information";
$txt_to_write = "DELETE";
$write_here_delete = "Write here \"".$txt_to_write."\"";
$do_not_delete_account = "I've been thinking and I want to keep my account on ".$name_website.".";
$i_dont_want_to_delete_my_account = "I want to keep my account";

$account_not_deleted_successfully = "Your account has not been deleted! Phew...";

$account_deleted_successfully = "Your account and all related information have been permanently deleted. You will be disconnected permanently in 5 seconds...";
$wrong_txt_to_write = "You have not entered the correct text.";
<?php
include 'kernel/config.php';
$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
mysql_select_db($user,$db)  or die('Erreur de selection '.mysql_error()); 

$query = mysql_query("SELECT DISTINCT (
a.email
), a.username
FROM teen_quotes_account a, teen_quotes_comments c
WHERE c.auteur_id = a.id
AND a.location_signup =  'appiOS'
ORDER BY a.email ASC");
$i = 1;

while ($data = mysql_fetch_array($query))
{
	$username = $data['username'];
	$email = $data['email'];

	$subject = "Problem about comments posted from the app";
	$message = 'Hello <font color="#394DAC"><b>'.$username.'</b></font>,<br/>
	<br/>
	You receive this email because you have created your Teen Quotes account from the application and because you\'ve already posted a comment.<br/>
	<br/>
	We\'re very sorry to tell you that we have identified an issue related to the post of a comment from the application when your comment contains an apostrophe \'. It could be possible that your comment was not posted entirely.
	<br/><br/>
	Please check your comments you\'ve written and if you want to edit your previous comment, send us an email at support@teen-quotes.com with your new comment, the ID of the quote and your username.<br/><br/>
	We\'re working to solve the problem. Sorry for the inconvenience.
	<br/><br/>
	Best regards,<br/>
	<b>The Teen Quotes Team</b>';

	mail($email, $subject, $top_mail.$message.$end_mail, $headers);
	echo '<b>#'.$i.'</b> - '.$email.'<br/>';
	$i++;
}
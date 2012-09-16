<?php
if (date('M') == 'Dec' AND date('d') >= 10)
{
	require "kernel/config.php";
	$db = mysql_connect($host, $user, $pass)  or die('Erreur de connexion '.mysql_error());
	mysql_select_db($user,$db)  or die('Erreur de selection '.mysql_error()); 
	require "kernel/fonctions.php";

	$query = mysql_query("SELECT email, username FROM teen_quotes_account WHERE id IN ('27', '2006', '2030', '842', '1936')");

	while ($donnees = mysql_fetch_array($query))
	{
		$username = $donnees['username'];
		$email = $donnees['email'];

		$message = $top_mail.'
		Hi <font color="#5C9FC0"><b>'.$username.'</b></font>!<br/>
		<br/>
		Today we’ve got a big annoucement for you! <b>Teen Quotes is now available right from your iPhone or your iTouch</b> thanks to our brand new application.<br/>
		<br/>
		Do not ever leave Teen Quotes. Free, easy to use and fast, this application offers the website\'s best functionalities.<br/>
		<br/>
		<ul>
			<li>Browse quotes, <font color="#5C9FC0">even if you\'re offline</font>.</li>
			<li>Create your account, or sign in if you have already one.</li>
			<li>Submit quotes and add comments.</li>
			<li>Share on Facebook, Twitter and via email.</li>
			<li>Add quotes to your favorites.</li>
		</ul>
		You can download the application right now : visit <a href="http://teen-quotes.com/apps" title="Teen Quotes application">teen-quotes.com/apps</a> from your iPhone / iTouch.<br/>
		<br/>
		See you soon on Teen Quotes.<br/>
		<br/>
		Best regards,<br/>
		<b>The Teen Quotes Team</b>'.$end_mail;

		mail($email, 'iPhone / iTouch application', $message, $headers);
	}
}
else
{
	echo 'Nein.';
}
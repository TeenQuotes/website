<?php 
include 'header.php';
echo '
	<div class="post">
		<h2><img src="http://www.teen-quotes.com/images/icones/signin.png" class="icone_login" />'.$sign_in.'</h2>';
require "connexion.php";
echo '
		<div class="grey_post">
			<form action="?action=connexion" method="post">
				<img src="http://www.teen-quotes.com/images/icones/membre.png" class="icone_login" /><input type="text" name="pseudo" style="width:115px;margin-bottom:10px;"/><br>
				<img src="http://www.teen-quotes.com/images/icones/password.png" class="icone_login" /><input type="password" name="pass" style="width:115px"/>
				<p align="right"><input type="submit" name="connexion" class="submit" value="'.$log_me.'"/></p>
			</form>
		</div>
		<span class="right"><a href="signup" title="'.$sign_up.'">'.$sign_up.'</a> | <a href="forgot" title="'.$forget.'"> '.$forget.'</a></span><br>
	</div>
';
	
include "footer.php";
?>
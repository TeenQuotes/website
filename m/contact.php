<?php 
include 'header.php';
include '../lang/'.$language.'/contact.php';

echo '
<div class="post">
	<h2><img src="http://'.$domaine.'/images/icones/contact.png" class="icone" />Contact</h2>
	<img src="http://'.$domaine.'/images/icones/mail.png" class="icone" /> '.$about_website.' : <a href="mailto:support@teen-quotes.com">support@teen-quotes.com</a><br>
	<br />
	<img src="http://'.$domaine.'/images/icones/mail.png" class="icone" /> '.$about_twitter_account.' : <a href="mailto:contact@teen-quotes.com">contact@teen-quotes.com</a><br>
	<br />
	<img src="http://'.$domaine.'/images/icones/antoine.png" class="icone" />Antoine Augusti - '.$developer.' : <a href="http://www.antoine-augusti.fr" target="_blank">www.antoine-augusti.fr</a><br>
	<br />
	<img src="http://www.pretty-web.com/images/icones/frog.png" class="icone">'.$partner.' : <a href="http://www.pretty-web.com" target="_blank">Pretty Web</a><br>
	<br />
</div>
';

include "footer.php"; 
?>
<?php 
include 'header.php';
include '../lang/'.$language.'/contact.php';
?>
<div class="post">
	<h2><img src="http://www.teen-quotes.com/images/icones/contact.png" class="icone" />Contact</h2>
	<img src="http://www.teen-quotes.com/images/icones/mail.png" class="icone" /> <?php echo $about_website; ?> : <a href="mailto:contact@pretty-web.com">contact@pretty-web.com</a><br>
	<br />
	<img src="http://www.teen-quotes.com/images/icones/mail.png" class="icone" /> <?php echo $about_twitter_account; ?> : <a href="mailto:contact@teen-quotes.com">contact@teen-quotes.com</a><br>
	<br />
	<img src="http://www.teen-quotes.com/images/icones/antoine.png" class="icone" />Antoine Augusti - <?php echo $developer; ?> : <a href="http://www.antoine-augusti.fr" target="_blank">www.antoine-augusti.fr</a><br>
	<br />
	<img src="http://www.pretty-web.com/images/icones/frog.png" class="icone"><?php echo $partner; ?> : <a href="http://www.pretty-web.com" target="_blank">Pretty Web</a><br>
	<br />
</div>
<?php include "footer.php"; ?>
<?php 
include 'header.php';
include '../lang/'.$language.'/stories.php';

echo '
	<div class="post">
		<h2><img src="http://'.$domaine.'/images/icones/stories.png" class="icone" />'.$stories.'</h2>
		'.$stories_not_available_mobile.'
	</div>';

include 'footer.php';
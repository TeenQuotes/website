<?php 
include 'header.php';
include '../lang/'.$language.'/statistics.php';

echo '
	<div class="post">
		<h2><img src="http://'.$domain.'/images/icones/test.png" class="icone" />'.$statistics.'</h2>
		'.$statistics_not_available_mobile.'
	</div>';

include 'footer.php';
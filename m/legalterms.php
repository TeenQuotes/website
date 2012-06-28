<?php 
include 'header.php';
include '../lang/'.$language.'/legalterms.php';

echo '
<div class="post">
	<h2><img src="http://teen-quotes.com/images/icones/balance.png" class="icone" />'.$legal_terms.'</h2>
	'.$texte_legal.'
</div>';

include "footer.php";
?>
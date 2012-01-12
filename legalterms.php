<?php 
include 'header.php';
include 'lang/'.$language.'/legalterms.php';

echo '
<div class="post">
<h1><img src="http://'.$domaine.'/images/icones/balance.png" class="icone" />'.$legal_terms.'</h1>
'.$texte_legal.'
</div>
';

include "footer.php"; 
?>
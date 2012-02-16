<?php 
include "header.php";
include "../lang/$language/error.php"; 
$error = mysql_real_escape_string($_GET['erreur']);
// 404
if ($error=="404") { ?>
<div class="post">
<h2><img src="http://www.teen-quotes.com/images/icones/erreur.png" class="icone" /><?php echo $error; ?></h2>
<br />
<br />
<?php echo $texte_error_404; ?>
</div>
<?php }
// 403 
elseif ($error=="403") { ?>
<div class="post">
<h2><img src="http://www.teen-quotes.com/images/icones/erreur.png" class="icone" /><?php echo $error; ?></h2>
<?php echo $texte_error_403; ?>
</div>
<?php }
// 500
elseif ($error=="500") { ?>
<div class="post">
<h2><img src="http://www.teen-quotes.com/images/icones/erreur.png" class="icone" /><?php echo $error; ?></h2>
<?php echo $texte_error_500; ?>
</div>
<?php }
else { ?>
<div class="post">
<h2>Oops ! Error !</h2>
Something is technically wrong, please refresh and if it often happens, contact us !
</div>
<?php }
include'footer.php'; ?>
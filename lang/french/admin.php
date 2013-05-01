<?php
$days_quote_posted = "jour";
$quote_added_queue = "Citation ajoutée à la file d'attente";
$quote_rejected    = "Citation refusée";
$comment_deleted   = "Commentaire supprimé";

// Moderation (see also /kernel/send_moderation.php)
$edit_message = '<br/><b>Votre citation a été modifiée par notre équipe avant son approbation. Veuillez respecter la syntaxe, l\'orthographe et le sens de votre citation.</b>';
$email_subject_moderate_quote = 'Modération de vos citations';

$final_mail = ''.$top_mail.'Bonjour <font color="#394DAC"><b>'.$name_author.'</b></font> !<br/><br/>';
$quotes_unapproved_reasons = '
<ul>
	<li>Votre citation était trop courte.</li>
	<li>Votre citation existait déjà sur '.$name_website.'</li>
	<li>Votre citation n\'était pas assez originale.</li>
	<li>Votre citation contenait trop de fautes d\'orthographe.</li>
</ul>
Ne vous inquiétez pas, vos citations seront approuvées un jour !<br/>';
$quotes_unapproved_plural   = 'Vos citations ont été rejetées pour une des raisons suivantes :';
$quotes_unapproved_singular = 'Votre citation a été rejetée pour une des raisons suivantes :';
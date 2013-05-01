<?php
$days_quote_posted = "day";
$quote_added_queue = "Quote added to the queue";
$quote_rejected    = "Quote rejected";
$comment_deleted   = "Comment deleted";

// Moderation (see also /kernel/send_moderation.php)
$edit_message = '<br/><b>Your quote has been modified by our team before approval. Please be careful the syntax, the spelling and the meaning of your quote.</b>';
$email_subject_moderate_quote = 'Moderation of your quotes';

$final_mail = ''.$top_mail.'Hello <font color="#394DAC"><b>'.$name_author.'</b></font>!<br/><br/>';
$quotes_unapproved_reasons = '
<ul>
	<li>Your quote was too short.</li>
	<li>Your quote already exists on '.$name_website.'</li>
	<li>Your quote was not original.</li>
	<li>Your quote contains spelling mistakes.</li>
</ul>
Do not worry, your quotes will be approved one day!<br/>';
$quotes_unapproved_plural   = 'Your quotes were rejected for one of the following reasons:';
$quotes_unapproved_singular = 'Your quote was rejected for one of the following reasons:';
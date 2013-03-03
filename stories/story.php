<?php
include 'header.php';
$id_story = intval($_GET['id_story']);
$query = mysql_query("SELECT
					s.id id_story, s.txt_represent txt_represent, s.txt_frequence txt_frequence, s.timestamp date, a.username username, a.id id_user, a.avatar avatar
					FROM teen_quotes_account a, stories s
					WHERE a.id = s.id_user AND s.id = $id_story
					ORDER BY s.id DESC");
echo '
<div class="post">
	<h1 class="blue">'.$story.' #'.$id_story.'</h1>';

	if (mysql_num_rows($query) == 1)
	{
		display_individual_story(mysql_fetch_array($query));
	}
	else
	{
		echo $story_do_not_exist;
	}
	
	echo '
	<br/>
	<center><a href="//stories.'.$domain.'" class="bouton bouton-bleu" title="'.$add_your_story.'">'.$add_your_story.'</a></center>
</div>';

include 'footer.php';
?>
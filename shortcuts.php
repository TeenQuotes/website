<?php 
include 'header.php';
include 'lang/'.$language.'/shortcuts.php';

echo '
	<div class="post">
		<h2><img src="http://'.$domain.'/images/icones/keyboard.png" class="icone" />'.$keyboard_shortcuts.'</h2>
		<div class="grey_post">
			'.$text_shortcuts.'
		</div>
	</div>';

include 'footer.php';
?>
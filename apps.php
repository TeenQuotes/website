<?php 
include 'header.php';
include 'lang/'.$language.'/apps.php';

echo '
<div class="post">
	<h1><img src="http://'.$domaine.'/images/icones/mobile.png" class="icone" />'.$applications.'</h1>
	<div class="grey_post">
	'.$text_applications.'';
	
	if ($link_app_iphone == '#')
		{
		echo ''.$app_iphone_not_available.'';
		}
	if ($link_app_android == '#')
		{
		echo ''.$app_android_not_available.'';
		}
		
	echo '
	</div>
	
	<div class="img_apps left">
		<a href="'.$link_app_iphone.'" target="_blank"><img src="http://'.$domaine.'/images/icones/app_iphone.png" class="apps" /><br /><img src="http://'.$domaine.'/images/icones/app_store_'.$language.'.png" class="download_app" /></a>
	</div>
	<div class="img_apps right">
		<a href="'.$link_app_android.'" target="_blank"><img src="http://'.$domaine.'/images/icones/app_android.png" class="apps" /><br /><img src="http://'.$domaine.'/images/icones/android_market_'.$language.'.png" class="download_app" /></a>
	</div>
	<div class="clear"></div>
</div>
';

include "footer.php"; 
?>
<?php 
include 'header.php';
include 'lang/'.$language.'/apps.php';
$action = htmlspecialchars($_GET['action']);

if (empty($action))
{	
	echo '
	<div class="post">
		<h1><img src="http://'.$domaine.'/images/icones/mobile.png" class="icone" />'.$applications.'</h1>
		<div id="app_ios" class="grey_post">
			'.$text_applications.'';
}
elseif ($action == 'disconnect')
{
	echo '
	<div class="post">
		<h1><img src="http://'.$domaine.'/images/icones/mobile.png" class="icone" />'.$signed_out_go_mobile.'</h1>
		<div id="app_ios" class="grey_post">
		'.$text_applications.'';
}
elseif ($action == 'mobile')
{
	echo '
	<div class="post">
		<h1><img src="http://'.$domaine.'/images/icones/mobile.png" class="icone" />'.$signed_out_go_mobile.'</h1>
		<div class="img_mobile_website"></div>
		<div class="grey_post div_presentation_mobile_website">
			'.$text_mobile_website.'
		</div>
		<div class="clear"></div>
	</div>';
}

if (empty($action) OR $action == 'disconnect')
{	
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
			<a href="'.$link_app_iphone.'" onClick="_gaq.push([\'_trackEvent\', \'appiOS\', \'clic\', \'Website - redirect iTunes - page /apps\']);" target="_blank"><img src="http://'.$domaine.'/images/icones/app_iphone.png" class="apps" /><br/><img src="http://'.$domaine.'/images/icones/app_store_'.$language.'.png" class="download_app" /></a>
		</div>
		
		<div class="clear"></div>';
	if ($action == 'disconnect')
	{
		echo '
		<h1><img src="http://'.$domaine.'/images/icones/mobile.png" class="icone" />'.$mobile_website.'</h1>
		<div class="img_mobile_website"></div>
		<div class="grey_post div_presentation_mobile_website">
			'.$text_mobile_website.'
		</div>
		<div class="clear"></div>';
	}

	echo '</div>';
}

include "footer.php"; 
?>
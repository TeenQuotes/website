<?php 
include 'header.php';
include '../lang/'.$language.'/apps.php';

$user_agent = $_SERVER['HTTP_USER_AGENT'];

if (mb_eregi('ipod', $user_agent) OR mb_eregi('iphone', $user_agent) AND $link_app_iphone != '#')
{
	echo '<meta http-equiv="refresh" content="0;URL=\''.$link_app_iphone.'\'">';
}
elseif (mb_eregi('android', $user_agent) AND $link_app_android != '#')
{
	echo '<meta http-equiv="refresh" content="0;URL=\''.$link_app_android.'\'">';
}
else
{
	if (empty($action))
	{	
		echo '
		<div class="post">
			<h2><img src="http://'.$domain.'/images/icones/mobile.png" class="icone" />'.$applications.'</h2>
			<div class="grey_post">
				'.$text_applications;
	}
	elseif ($action == 'disconnect')
	{
		echo '
		<div class="post">
			<h2><img src="http://'.$domain.'/images/icones/mobile.png" class="icone" />'.$signed_out_go_mobile.'</h2>
			<div class="grey_post">
				'.$text_applications;
	}
	elseif ($action == 'mobile')
	{
		echo '
		<div class="post">
			<h2><img src="http://'.$domain.'/images/icones/mobile.png" class="icone" />'.$signed_out_go_mobile.'</h2>
			<div class="img_mobile_website"></div>
			<div class="grey_post div_presentation_mobile_website">
				'.$text_mobile_website.'
			</div>
			<div class="clear"></div>';
	}

	if (empty($action) OR $action == 'disconnect')
	{	
		if ($link_app_iphone == '#')
		{
			echo $app_iphone_not_available;
		}
		if ($link_app_android == '#')
		{
			echo $app_android_not_available;
		}
	}

	echo '</div>';
}

include "footer.php"; 
<?php 
include 'header.php';
include 'lang/'.$language.'/apps.php';
$action = htmlspecialchars($_GET['action']);

if (empty($action))
{	
	?>
	<div class="post">
		<h1><img src="http://<?php echo $domain; ?>/images/icones/mobile.png" class="icone" /><?php echo $applications; ?></h1>
		<div id="app_ios" class="grey_post">
			<?php echo $text_applications;
}
elseif ($action == 'disconnect')
{
	?>
	<div class="post">
		<h1><img src="http://<?php echo $domain; ?>/images/icones/mobile.png" class="icone" /><?php echo $signed_out_go_mobile; ?></h1>
		<div id="app_ios" class="grey_post">
		<?php echo $text_applications;
}
elseif ($action == 'mobile')
{
	?>
	<div class="post">
		<h1><img src="http://<?php echo $domain; ?>/images/icones/mobile.png" class="icone" /><?php echo $signed_out_go_mobile; ?></h1>
		<div class="img_mobile_website"></div>
		<div class="grey_post div_presentation_mobile_website">
			<?php echo $text_mobile_website; ?>
		</div>
		<div class="clear"></div>
	</div>

	<?php
}

if (empty($action) OR $action == 'disconnect')
{	
		if ($link_app_iphone == '#')
			echo $app_iphone_not_available;
		if ($link_app_android == '#')
			echo $app_android_not_available;
		?>
		</div>
		
		<div class="img_apps left">
			<a href="<?php echo $link_app_iphone; ?>" onClick="_gaq.push([\'_trackEvent\', \'appiOS\', \'clic\', \'Website - redirect iTunes - page /apps\']);" target="_blank"><img src="http://<?php echo $domain; ?>/images/icones/app_iphone.png" class="apps" /><br/><img src="http://<?php echo $domain; ?>/images/icones/app_store_<?php echo $language; ?>.png" class="download_app" /></a>
		</div>
		
		<div class="clear"></div>

		<h1><img src="http://<?php echo $domain; ?>/images/icones/mobile.png" class="icone" /><?php echo $mobile_website; ?></h1>
		<div class="img_mobile_website"></div>
		<div class="grey_post div_presentation_mobile_website">
			<?php echo $text_mobile_website; ?>
		</div>
		<div class="clear"></div>


	</div>

	<?php
}

include "footer.php"; 
?>
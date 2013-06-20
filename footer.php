			</div><!-- END WRAPPER -->

			<div id="right" <?php if ($_SERVER['PHP_SELF']=='/index.php') {echo "style=\"margin-top:35px\"";} ?>>
			
				<div id="lancement_app_ios">
					<a href="<?php echo $link_app_iphone; ?>" <?php hint('top', $apps_hint); ?> onClick="_gaq.push(['_trackEvent', 'appiOS', 'clic', 'Website - sidebar right']);" title="App iOS"></a>
				</div>
				
				<div class="post">
					<div class="title"><span class="icone_login search"></span><?php echo $search; ?></div>
					<form action="search" method="get">
					<input type="text" name="q" style="width:115px;margin-bottom:10px;" <?php echo $search_value_form; ?>/><br/>
					<input type="submit" class="submit" value="<?php echo $search; ?>"/>
					</form>
				</div>
			
				<?php if ($_SESSION['logged'] != true) { ?>
				
				<div class="post">
					<div class="title"><span class="icone_login signin"></span><?php echo $sign_in; ?></div>
					<?php 
					include 'lang/'.$language.'/connexion.php';
					require 'kernel/connexion.php'; ?>

					<form action="?action=connexion" method="post">
						<span class="icone_login member"></span><input type="text" name="pseudo" placeholder="<?php echo $username_form; ?>" class="input_right_connexion_form"/>
						<span class="icone_login password"></span><input type="password" name="pass" placeholder="<?php echo $password_form; ?>" class="input_right_connexion_form"/>
						<span class="right margin_log_me"><input type="submit" name="connexion" class="submit" value="<?php echo $log_me; ?>"/></span>
						<div class="clear"></div>
					</form>

					<span class="right">
						<a href="signup?menuright" <?php hint('top', $sign_up_hint); ?> onClick="_gaq.push(['_trackEvent', 'signup', 'clic', 'Website - right menu']);"><?php echo $sign_up; ?></a>
						 | 
						<a href="forgot"> <?php echo $forget; ?></a>
					</span><br/>
				</div>
				<?php } else { ?>
				<div class="post" id="logged_box">
					<?php
					if ($domain == $domain_en)
					{
					?>
						<div class="title"><span class="icone_login member"></span><?php echo $my_account; ?></div>
						<?php echo $connected_as; ?> <a href="user-<?php echo $_SESSION['id'] ?>" <?php hint('right', $my_profile_hint); ?>><?php echo $username; ?></a><br/>
						<br/>
						<form action="?deconnexion" method="post">
							<input type="submit" value="<?php echo $logout; ?>" />
						</form>
						<form action="../editprofile" method="post" style="margin-top:-30px;float:right">
							<input type="submit" value="<?php echo $edit; ?>" />
						</form>
					<?php
					}
					else
					{
					?>
						<div class="title"><span class="icone_login member"></span><?php echo $my_account; ?></div>
						<?php echo $connected_as; ?> <span class="bleu"><?php echo $username; ?></span><br/>
						<br/>
						<a href="user-<?php echo $_SESSION['id'] ?>" title="<?php echo $my_profile; ?>">&raquo; <?php echo $my_profile; ?></a><span class="right"><form action="../editprofile" method="post"><input type="submit" value="<?php echo $edit; ?>" /></form></span><br/>
						<a href="?deconnexion" title="<?php echo $log_out; ?>">&raquo; <?php echo $logout; ?> </a><br/>
					<?php
					}
					?>
				</div>
				<?php } ?>
				
				<?php 
				if ($show_pub == '1')
				{
					echo
					'
					<div class="pub_footer">
						<script type="text/javascript"><!--
						google_ad_client = "ca-pub-8130906994953193";
						/* Annonce menu 2 */
						google_ad_slot = "3004031257";
						google_ad_width = 160;
						google_ad_height = 600;
						//-->
						</script>
						<script type="text/javascript"
						src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
						</script>
					</div>';
				}
				?>
				
			</div><!-- END RIGHT -->
			
		</div><!-- END CONTENT -->

		<div class="clear"></div>

		<div id="footer">
			<div class="content">
				<div class="left">
					<?php echo $footer_description; ?>
				</div>

				<div class="right">
					<?php
					echo $name_website.' &copy; '.date("Y");
					?>
					<br/>
					<br/>
					<a href="//m.<?php echo $domain; ?>/<?php echo $php_self; ?>"><?php echo $mobile_website; ?></a><br/>
					<a href="//stories.<?php echo $domain; ?>" title="<?php echo $stories; ?>" onClick="_gaq.push(['_trackEvent', 'stories', 'clic', 'Footer']);"><?php echo $stories; ?></a> &bull; <a href="//<?php echo $domain; ?>/advertise" title="<?php echo $advertise; ?>"><?php echo $advertise; ?></a><br/>
					<a href="//statistics.<?php echo $domain; ?>" title="<?php echo $statistics; ?>"><?php echo $statistics; ?></a> &bull; <a href="//<?php echo $domain; ?>/shortcuts" title="<?php echo $keyboard_shortcuts; ?>"><?php echo $keyboard_shortcuts; ?></a><br/>
					<a href="//<?php echo $domain; ?>/contact" title="Contact">Contact</a> &bull; <a href="//<?php echo $domain; ?>/legalterms" title="<?php echo $legal_terms; ?>"><?php echo $legal_terms; ?></a><br/>
					<br/>
					<span id="caption_footer">Designed in Paris. <span id="eiffel-tower"></span></span><br/>
				</div>

				<div class="clear"></div>
			</div>
		</div><!-- END FOOTER -->

			<?php 
				mysql_close();
			?>
		
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if (!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			<script src="//<?php echo $domain; ?>/scrypt.min.js"></script>
	</body>
</html>
<!-- 

<?php 
$time_end = microtime_float(); 
$time = round($time_end - $time_start, 4); // 4 chiffres àpres la virgule 
echo "Page générée en ", $time, "s"; 
?>

END CODE :) -->
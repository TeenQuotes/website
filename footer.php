			</div><!-- END WRAPPER -->

			<div id="right" <?php if ($_SERVER['PHP_SELF']=='/index.php') {echo "style=\"margin-top:35px\"";} ?>>
				<div class="post">
					<div class="title"><span class="icone_login search"></span><?php echo $search; ?></div>
					<form action="search" method="get">
					<input type="text" name="q" style="width:115px;margin-bottom:10px;" <?php echo $search_value_form; ?>/><br>
					<input type="submit" class="submit" value="<?php echo $search; ?>"/>
					</form>
				</div>
			
				<?php if ($_SESSION['logged'] != TRUE) { ?>
				
				<div class="post">
					<div class="title"><span class="icone_login signin"></span><?php echo $sign_in; ?></div>
					<?php require "connexion.php"; ?>
					<form action="?action=connexion" method="post">
						<span class="icone_login member"></span><input type="text" name="pseudo" class="input_right_connexion_form"/>
						<span class="icone_login password"></span><input type="password" name="pass" class="input_right_connexion_form"/>
						<span class="right margin_log_me"><input type="submit" name="connexion" class="submit" value="<?php echo $log_me; ?>"/></span>
						<div class="clear"></div>
					</form>
					<span class="right"><a href="signup?menuright" title="<?php echo $sign_up; ?>"><?php echo $sign_up; ?></a> | <a href="forgot" title="<?php echo $forget; ?>"> <?php echo $forget; ?></a></span><br>
				</div>
				<?php } else { ?>
				<div class="post">
					<div class="title"><span class="icone_login member"></span><?php echo $my_account; ?></div>
					<?php echo $connected_as; ?> <span class="bleu"><?php echo $username; ?></span><br>
					<br />
					<a href="user-<?php echo $_SESSION['id'] ?>" title="<?php echo $my_profile; ?>">&raquo; <?php echo $my_profile; ?></a><span class="right"><form action="../editprofile" method="post"><input type="submit" value="<?php echo $edit; ?>" /></form></span><br>
					<a href="?deconnexion" title="<?php echo $log_out; ?>">&raquo; <?php echo $logout; ?> </a><br>
					
				</div>
				<?php } ?>
				
				
				<div class="post">
					<div class="title"><span class="icone_login about"></span><?php echo $about; ?></div>
					<div class="font_size_85">
					&copy; <?php echo date("Y"); ?> <?php echo $domaine; ?><br>
					<?php echo $created_by; ?> <a href="http://www.antoine-augusti.fr" target="_blank"> Antoine Augusti</a>
					<br><?php echo $developer; ?> <a href="http://www.pretty-web.com" target="_blank">Pretty Web</a><br>
					&raquo; <a href="business" title="<?php echo $business; ?>"><?php echo $business; ?></a><br>
					&raquo; <a href="statistics" title="<?php echo $statistics; ?>"><?php echo $statistics; ?></a><br>
					&raquo; <a href="shortcuts" title="<?php echo $keyboard_shortcuts; ?>"><?php echo $keyboard_shortcuts; ?></a><br>
					&raquo; <a href="contact" title="Contact">Contact</a><br>
					&raquo; <a href="legalterms" title="<?php echo $legal_terms; ?>"><?php echo $legal_terms; ?></a><br>
					</div>
				</div>
				
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
			<?php
			if ($domaine == 'kotado.fr')
			{
				echo 'Kotado &copy '; echo date("Y"); echo ' | Teen Quotes : <a href="http://teen-quotes.com" title="Teen Quotes" target="_blank">teen-quotes.com</a>';
			}
			else
			{
				echo 'Teen Quotes &copy '; echo date("Y"); echo ' | Kotado : <a href="http://kotado.fr" title="Kotado" target="_blank">kotado.fr</a>';
			}
			?>
			<span class="right">
				<a href="http://m.<?php echo $domaine; ?>/<?php echo $php_self; ?>"><?php echo $mobile_website; ?></a> |
				<a href="contact">Contact</a> |
				<a href="legalterms"><?php echo $legal_terms; ?></a>
			</span>
		</div><!-- END FOOTER -->

		<?php mysql_close(); ?>
		
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			<?php 
			if ($php_self=="search")
				{
				echo '<script type="text/javascript" src="http://static.augusti.fr/js/scroll.js"></script>';
				}
			?>
			<script src="http://<?php echo $domaine; ?>/scrypt.min.js"></script>
	</body>
</html>
<!-- 

<?php 
$time_end = microtime_float(); 
$time = round($time_end - $time_start, 4); // 4 chiffres àpres la virgule 
echo "Page générée en ", $time, "s"; 
?>

END CODE :) -->
			</div><!-- END WRAPPER -->

			<div id="right" <?php if ($_SERVER['PHP_SELF']=='/index.php') {echo "style=\"margin-top:35px\"";} ?>>
				<div class="post">
					<div class="title"><span class="icone_login search"></span><?php echo $search; ?></div>
					<form action="search" method="get">
					<input type="text" name="q" style="width:115px;margin-bottom:10px;" <?php echo $search_value_form; ?>/><br>
					<input type="submit" class="submit" value="<?php echo $search; ?>"/>
					</form>
				</div>
			
				<?php if (!$_SESSION['logged']) { ?>
				
				<div class="post">
					<div class="title"><span class="icone_login signin"></span><?php echo $sign_in; ?></div>
					<?php require "connexion.php"; ?>
					<form action="?action=connexion" method="post">
					<span class="icone_login member"></span><input type="text" name="pseudo" style="width:115px;margin-bottom:10px;"/>
					<span class="icone_login password"></span><input type="password" name="pass" style="width:115px"/>
					<p align="right"><input type="submit" name="connexion" class="submit" value="<?php echo $log_me; ?>"/></p>
					</form>
					<span class="right"><a href="signup?menuright" title="<?php echo $sign_up; ?>"><?php echo $sign_up; ?></a> | <a href="forgot" title="<?php echo $forget; ?>"> <?php echo $forget; ?></a></span><br>
				</div>
				<?php } else { ?>
				<div class="post">
					<div class="title"><span class="icone_login member"></span><?php echo $my_account; ?></div>
					<?php echo $connected_as; ?> <span class="bleu"><?php echo ucfirst($username); ?></span><br>
					<br />
					<a href="user-<?php echo $_SESSION['account'] ?>">&raquo; <?php echo $my_profile; ?></a><span class="right"><a href="editprofile" class="submit" style="text-decoration:none"><?php echo $edit; ?></a></span><br>
					<a href="?deconnexion" title="<?php echo $log_out; ?>">&raquo; <?php echo $logout; ?> </a><br>
					
				</div>
				<?php } ?>
				
				
				<div class="post">
					<div class="title"><span class="icone_login about"></span><?php echo $about; ?></div>
					<p style="font-size:85%">
					&copy; <?php echo date("Y"); ?> teen-quotes.com<br>
					<?php echo $created_by; ?> <a href="http://www.antoine-augusti.fr" target="_blank"> Antoine Augusti</a>
					<br><?php echo $developer; ?> <a href="http://www.pretty-web.com" target="_blank">Pretty Web</a><br>
					&raquo; <a href="contact" title="Contact">Contact</a><br>
					&raquo; <a href="legalterms"><?php echo $legal_terms; ?></a><br>
					<?php if($language=='french'){?>&raquo; <a href="http://www.teen-quotes.com/project/">Présentation du projet</a><?php } ?></p>
				</div>
				
				<?php 
				if ($show_pub == '1')
					{
					echo
					'
					<div class="pub_footer">
					<script type="text/javascript"><!--
					google_ad_client = "ca-pub-8130906994953193";
					/* Annonce menu */
					google_ad_slot = "3852684135";
					google_ad_width = 120;
					google_ad_height = 600;
					//-->
					</script>
					<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
					</div>';
					}
				?>
				
			</div><!-- END RIGHT -->
			
		</div><!-- END CONTENT -->

		<div class="clear"></div>

		<div id="footer">
			Teen Quotes &copy; <?php echo date("Y"); ?> 
			<span class="right">
				<?php if($language=='french') {?>
				<a href="http://www.teen-quotes.com/project/">Présentation du projet</a> |
				<?php } ?>
				<a href="http://m.teen-quotes.com/<?php echo $php_self; ?>"><?php echo $mobile_website; ?></a> |
				<a href="contact">Contact</a> |
				<a href="legalterms"><?php echo $legal_terms; ?></a>
			</span>
		</div><!-- END FOOTER -->

		<?php mysql_close(); ?>
		
			<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
			<script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
			<?php 
			if ($php_self=="search")
				{
				echo '<script type="text/javascript" src="http://static.augusti.fr/js/scroll.js"></script>';
				}
			?>
			<script> 
			$(".hide").click(function () {
			$(".profile_not_fullfilled").slideUp("slow");
			});
			$(".hide_this").click(function () {
			$(this).slideUp("slow");
			});			
			</script> 
			<script type="text/javascript">
			$(function(){
			$('.slidedown').slideDown(1500);
			});
			</script>
	</body>
</html>
<!-- 

<?php 
$time_end = microtime_float(); 
$time = round($time_end - $time_start, 4); // 4 chiffres àpres la virgule 
echo "Page générée en ", $time, "s"; 
?>

END CODE :) -->
			</div><!-- END WRAPPER -->

			
		</div><!-- END CONTENT -->

		<div class="clear"></div>
		
		<?php 
		if ($show_pub == '1')
			{
			echo '
			<div class="pub">
			<script type="text/javascript"><!--
			google_ad_client = "ca-pub-8130906994953193";
			/* Footer - mobile */
			google_ad_slot = "9975010108";
			google_ad_width = 320;
			google_ad_height = 50;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
			</div>';
			}
		?>

		<div id="footer">
			Teen Quotes &copy; <?php echo date("Y"); ?> 
			<span class="right"><a href="http://www.teen-quotes.com/<?php echo $php_self; ?>?mobile"><?php echo $full_website; ?></a> |
			<a href="contact">Contact</a> |
			<a href="legalterms"><?php echo $legal_terms; ?></a></span>
		</div>

		<?php mysql_close(); ?>
	</body>
</html>
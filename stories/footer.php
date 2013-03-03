		<div class="clear"></div>
	</div>
	
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
				<a href="//<?php echo $domain; ?>/statistics" title="<?php echo $statistics; ?>"><?php echo $statistics; ?></a> &bull; <a href="//<?php echo $domain; ?>/shortcuts" title="<?php echo $keyboard_shortcuts; ?>"><?php echo $keyboard_shortcuts; ?></a><br/>
				<a href="//<?php echo $domain; ?>/contact" title="Contact">Contact</a> &bull; <a href="//<?php echo $domain; ?>/legalterms" title="<?php echo $legal_terms; ?>"><?php echo $legal_terms; ?></a><br/>
				<br/>
				<span id="caption_footer">Designed in Paris. <span id="eiffel-tower"></span></span><br/>
			</div>

			<div class="clear"></div>
		</div>
	</div><!-- END FOOTER -->
	<script src="//<?php echo $domain; ?>/scrypt.min.js"></script>

	<?php mysql_close(); ?>
</body>
</html>
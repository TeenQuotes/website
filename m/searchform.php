<?php 
include 'header.php'; ?>
<div class="post">
	<div class="title"><img src="http://www.teen-quotes.com/images/icones/search.png" class="icone_login" /><?php echo $search; ?></div>
	<form action="search" method="post">
	<input type="text" name="search" style="width:115px;margin-bottom:10px;" <?php echo $search_value_form; ?>/><br>
	<input type="submit" class="submit" value="<?php echo $search; ?>"/>
	</form>
</div>
<?php
include "footer.php"; ?>
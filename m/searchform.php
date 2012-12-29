<?php 
include 'header.php';
echo '
<div class="post">
	<h2><img src="http://teen-quotes.com/images/icones/search.png" class="icone_login" />'.$search.'</h2>
	<form action="search" method="get">
	<input type="text" name="q" style="width:115px;margin-bottom:10px;" '.$search_value_form.'/><br/>
	<input type="submit" class="submit" value="'.$search.'"/>
	</form>
</div>';
include "footer.php";
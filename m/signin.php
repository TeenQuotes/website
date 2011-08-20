<?php 
include 'header.php'; ?>
	<div class="post">
			<div class="title"><img src="http://www.teen-quotes.com/images/icones/signin.png" class="icone_login" /><?php echo $sign_in; ?></div>
			<?php require "connexion.php"; ?>
			<form action="?action=connexion" method="post">
			<img src="http://www.teen-quotes.com/images/icones/membre.png" class="icone_login" /><input type="text" name="pseudo" style="width:115px;margin-bottom:10px;"/><br>
			<img src="http://www.teen-quotes.com/images/icones/password.png" class="icone_login" /><input type="password" name="pass" style="width:115px"/>
			<p align="right"><input type="submit" name="connexion" class="submit" value="<?php echo $log_me; ?>"/></p>
			</form>
		<span class="right"><a href="signup" title="<?php echo $sign_up; ?>"><?php echo $sign_up; ?></a> | <a href="forgot" title="<?php echo $forget; ?>"> <?php echo $forget; ?></a></span><br>
	</div>
	
<?php include "footer.php";

 ?>
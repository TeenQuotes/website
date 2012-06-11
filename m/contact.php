<?php 
include 'header.php';
include '../lang/'.$language.'/contact.php';

$action = htmlspecialchars($_GET['action']);

if (empty($action))
	{
	echo '
	<div class="post">
		<h2><img src="http://'.$domaine.'/images/icones/contact.png" class="icone" />Contact</h2>
		<div class="grey_post">
			<img src="http://'.$domaine.'/images/icones/mail.png" class="icone" /> '.$about_website.' : <a href="mailto:support@teen-quotes.com">support@teen-quotes.com</a><br>
			<br />
			<img src="http://'.$domaine.'/images/icones/mail.png" class="icone" /> '.$about_twitter_account.' : <a href="mailto:contact@teen-quotes.com">contact@teen-quotes.com</a><br>
			<br />
			<img src="http://'.$domaine.'/images/icones/antoine.png" class="icone" />Antoine Augusti - '.$developer.' : <a href="http://www.antoine-augusti.fr" target="_blank">www.antoine-augusti.fr</a><br>
			<br />
			<img src="http://www.pretty-web.com/images/icones/frog.png" class="icone">'.$partner.' : <a href="http://www.pretty-web.com" target="_blank">Pretty Web</a><br>
		</div>

		<h2><img src="http://'.$domaine.'/images/icones/staff.png" class="icone" />'.$team.'</h2>
		<div class="grey_post">
			'.$team_txt.'
		</div>
		
		<h2><img src="http://'.$domaine.'/images/icones/mail.png" class="icone" />'.$contact_us_by_email.'</h2>
		<div class="grey_post">
			<form action="contact?action=send" method="post"> 
				'.$subject.' :<br>
				<input type="text" name="sujet" size="20" maxlength="30"><br> 
				<br /> 
				'.$your_name.' :<br>
				<input type="text" name="nom" size="20" maxlength="30"><br> 
				<br /> 
				'.$your_email.' :<br>
				<input type="email" name="email" size="20" maxlength="30"><br> 
				<br />';
				echo captcha();echo ' =<br>
				<input type="text" name="captcha" size="20" maxlength="30"><br> 
				<br />
				<textarea style="width:100%;height:50px" name="message" value="'.$enter_your_message_here.'" onblur="javascript:if(this.value==\'\'){this.value=\''.$enter_your_message_here.'\'}" onFocus="javascript:if(this.value==\''.$enter_your_message_here.'\'){this.value=\'\'}"/>'.$enter_your_message_here.'</textarea><br> 
				<br /> 
				'.$copie_of_this_email.' : <input type="checkbox" value="1" name="copie" checked/> 
				<center><input type="submit" name="submit" class="submit" value="'.$send.'"></center> 
			</form>
		</div>
	</div>
	';
	}
elseif ($action == 'send')
	{
	echo '
	<div class="post">
	<h2><img src="http://'.$domaine.'/images/icones/mail.png" class="icone" />'.$contact_us_by_email.'</h2>';
	
	if(isset($_POST['sujet']))      $sujet = $_POST['sujet'];
	else      $sujet = "";

	if (isset ($_POST ['copie'])) $copie = TRUE; 
	else $copie = FALSE; 

	if(isset($_POST['message']))      $message = $_POST['message'];
	else      $message = "";

	if(isset($_POST['email']) && preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email']))     $email = $_POST['email'];
	else      $email = "";


	if(isset($_POST['nom']))      $nom = $_POST['nom'];
	else      $nom = "";
	
	if(empty($sujet) OR empty($message) OR empty($email) OR empty($nom))
		{ 
		echo '<div class="bandeau_erreur">'.$input_empty.'</div>'.$lien_retour.'';
		}
	else      
		{
		if($_POST['captcha'] == $_SESSION['captcha'])
			{
		  
			$headers ='From: "'.$nom.'"<no-reply@'.$domaine.'>'."\n";
			$headers .='Reply-To: '.$email.''."\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .='Content-Type: text/plain; charset="iso-8859-1"'."\n";
			$headers .='Content-Transfer-Encoding: 8bit';
			$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
			 
			 
			$message .= "\r\n";
			$message .= "\r\n";
			$message .= '------------------ Message sent from www.'.$domaine.' ------------------';
			
			if(mail("support@teen-quotes.com", stripslashes($sujet), stripslashes($message), $headers))
				{ 
				echo ''.$succes.' '.$send_succes.' (<a href="mailto:'.$email.'">'.$email.'</a>)';
				}
			else
				{
				echo '<div class="bandeau_erreur">'.$error.'</div>'.$lien_retour.'';
				}

			$message .= "\r\n";
			$message .= "\r\n";
			$message .= "------------------ This is the copy of your message ------------------";

			if ($copie == TRUE && mail($email, stripslashes($sujet), stripslashes($message), "$headers"))
				{
				echo '<br /><br />'.$copy_sent.'';
				}
			}
		else 
			{
			echo '<div class="bandeau_erreur">'.$captcha_wrong.'</div>'.$lien_retour.'';
			}
		}
	echo '</div>';
	}

include "footer.php"; 
?>
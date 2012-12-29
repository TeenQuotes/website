<?php 
include 'header.php';
include 'lang/'.$language.'/contact.php';
include 'lang/'.$language.'/business.php';

$action = htmlspecialchars($_GET['action']);

if (empty($action))
{
	echo '
	<div class="post">
		<h2><img src="http://'.$domaine.'/images/icones/business.png" class="icone" />'.$advertise.'</h2>
			<div class="grey_post">
			'.$intro_txt.'
			</div>

		<h3><img src="http://'.$domaine.'/images/icones/delete.png" class="icone" />'.$what_we_dont_do.'</h3>
			<div class="grey_post">
			'.$what_we_dont_do_txt.'
			</div>

		<h3><img src="http://'.$domaine.'/images/icones/pricing.png" class="icone" />'.$pricing.'</h3>
			<div class="grey_post">
			'.$pricing_txt.'
			</div>

		<h3><img src="http://'.$domaine.'/images/icones/infos.png" class="icone" />'.$payment.'</h3>
			<div class="grey_post">
			'.$payment_txt.'
			</div>

		<h2><img src="http://'.$domaine.'/images/icones/mail.png" class="icone" />'.$contact_us_by_email.'</h2>
		<div class="grey_post">
			<form action="?action=send" method="post"> 
				'.$subject.' :<br/>
				<input type="text" name="sujet" size="20" maxlength="30"><br/> 
				<br/> 
				'.$your_name.' :<br/>
				<input type="text" name="nom" size="20" maxlength="30"><br/> 
				<br/> 
				'.$your_email.' :<br/>
				<input type="email" name="email" size="20" maxlength="30"><br/> 
				<br/>
				'.$your_twitter_account.' :<br/>
				<input type="text" name="twitter_account" value="@" size="20" maxlength="30"><br/>
				<br/>';
				echo captcha();echo ' =<br/>
				<input type="text" name="captcha" size="20" maxlength="30"><br/> 
				<br/>
				<textarea rows="10" cols="70" name="message" placeholder="'.$enter_your_message_here.'"/></textarea><br/> 
				<br/> 
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
	<h2><img src="http://'.$domaine.'/images/icones/business.png" class="icone" />'.$advertise.'</h2>';
	
	if(isset($_POST['sujet']))      $sujet = $_POST['sujet'];
	else      $sujet = "";

	if (isset ($_POST ['copie'])) $copie = TRUE; 
	else $copie = FALSE; 

	if(isset($_POST['message']))      $message = $_POST['message'];
	else      $message = "";

	if(isset($_POST['twitter_account']))      $twitter_account = $_POST['twitter_account'];
	else      $twitter_account = "";

	if(isset($_POST['email']) && preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $_POST['email']))     $email = $_POST['email'];
	else      $email = "";


	if(isset($_POST['nom']))      $nom = $_POST['nom'];
	else      $nom = "";
	
	if(empty($sujet) OR empty($message) OR empty($email) OR empty($nom) OR empty($twitter_account))
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
			$message .= 'Twitter\'s account : '.$twitter_account.''; 
			$message .= "\r\n";
			$message .= "\r\n";
			$message .= '------------------ Message sent from www.'.$domaine.' ------------------';
			
			if(mail("contact@teen-quotes.com", stripslashes($sujet), stripslashes($message), $headers))
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

			if ($copie == TRUE AND mail($email, stripslashes($sujet), stripslashes($message), "$headers"))
			{
				echo '<br/><br/>'.$copy_sent.'';
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
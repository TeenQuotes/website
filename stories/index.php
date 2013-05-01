<?php
include 'header.php';
?>
		<div class="post">
			<?php 
			if ($_SESSION['logged'] == true)
			{
			?>
				<h1><?php echo $tell_us_your_story.' <span class="blue">'.$username.'</span>!'; ?></h1>
				<img src="//<?php echo $domain; ?>/images/avatar/<?php echo $_SESSION['avatar']; ?>" class="story_avatar" title="<?php echo $username; ?>"/>
				<div class="grey_post story_description">
					<?php echo $story_description; ?>
				</div>
				<h1><?php echo $tell_us_how_you_use; ?></h1>
				<form method="post" action="/" id="submit_story">
					<input type="hidden" name="id_user" id="id_user" value="<?php echo $_SESSION['id']; ?>" />
					<input type="hidden" name="hash" id="hash" value="<?php echo $_COOKIE['Pass']; ?>" />
					<div class="grey_post form_story">
						<div id="notification"></div>
						<div class="left_form">
							<h2><?php echo $textarea_usage; ?></h2>
							<textarea required="required" id="story_usage" name="usage" placeholder="<?php echo $usage_placeholder; ?>"></textarea>
						</div>
						<div class="right_form">
							<h2><?php echo $textarea_frequence; ?></h2>
							<textarea required="required" id="story_frequence" name="frequence" placeholder="<?php echo $frequence_placeholder; ?>"></textarea>
						</div>
						<div class="clear"></div>
					</div>

					<center><p><input type="submit" class="no_uniform bouton bouton-bleu submit_story" value="<?php echo $share_my_story; ?>" /></p></center>
				</form>
			<?php
			}
			else
			{
			?>
				<h1><?php echo $not_logged; ?></h1>
				<form action="?action=connexion" method="post">
					<div class="left_form dark_gray_column light_shadow">
						<h2><?php echo $sign_in; ?></h2>
						<?php include '../kernel/connexion.php'; ?>
						<p><span class="icone_login member"></span><input type="text" name="pseudo" placeholder="<?php echo $username_form; ?>" required="required" class="input_right_connexion_form"/></p>
						<p><span class="icone_login password"></span><input type="password" name="pass" placeholder="<?php echo $password_form; ?>" required="required" class="input_right_connexion_form"/></p>
						<br/>
						<center><input type="submit" name="connexion" class="no_uniform bouton bouton-bleu" value="<?php echo $log_me; ?>"/></center>
					</div>
					<div class="right_form dark_gray_column light_shadow" style="height:180px">
						<h2><?php echo $sign_up; ?></h2>
						<p class="signup_text"><?php echo $create_account; ?></p>
						<center><a href="//<?php echo $domain; ?>/signup" class="bouton bouton-bleu" title="<?php echo $sign_up; ?>"><?php echo $sign_up; ?></a></center>
					</div>
					<div class="clear"></div>
				</form>
			<?php
			}
			?>
		</div>

		<div class="post">
			<h1 class="blue"><?php echo $your_stories; ?></h1>
			<?php
			$donnees = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS nb_messages FROM stories"));
			$nb_messages_par_page = 5;

			$display_page_top = display_page_top($donnees['nb_messages'], $nb_messages_par_page, 'p', $previous_page, $next_page, null, true);
			$premierMessageAafficher = $display_page_top[0];
			$nombreDePages = $display_page_top[1];
			$page = $display_page_top[2];

			$query = mysql_query("SELECT
								s.id id_story, s.txt_represent txt_represent, s.txt_frequence txt_frequence, s.timestamp date, a.username username, a.id id_user, a.avatar avatar
								FROM teen_quotes_account a, stories s
								WHERE a.id = s.id_user
								ORDER BY s.id DESC LIMIT $premierMessageAafficher, $nb_messages_par_page");
			while ($data = mysql_fetch_array($query))
			{
				display_individual_story($data);
			}

			display_page_bottom($page, $nombreDePages, 'p', null, $previous_page, $next_page, true);
			?>	
		</div>
<?php
include 'footer.php';
?>
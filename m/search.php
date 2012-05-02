<?php 
include 'header.php'; 
include '../lang/'.$language.'/search.php';
include '../lang/'.$language.'/user.php';
$value_search = htmlspecialchars(mysql_escape_string($_GET['q']));
echo '<script type="text/javascript" src="http://static.augusti.fr/js/highlight.js"></script>';
echo '<script type="text/javascript">$(\'#wrapper\').highlight(\''.$value_search.'\');</script>';

if (strlen($value_search) < '50')
	{
	$query = mysql_query("INSERT INTO teen_quotes_search (text) VALUES ('".$value_search."') ON DUPLICATE KEY UPDATE value = value+1");
	}

if (empty($value_search)) 
	{
	echo '<div class="post">
	<h2>'.$error.'</h2>
	'.$not_completed.'
	</div>';
	}
elseif (isset($value_search)) 
	{
	$num_rows_quote = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_quotes WHERE approved = '1' AND texte_english like '%".$value_search."%'"));
	$num_rows_members = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE username like '%".$value_search."%' AND hide_profile = '0'"));
	
	$num_rows_result = $num_rows_quote + $num_rows_members;

	if ($num_rows_quote >= '1' OR $num_rows_members >= '1') 
		{
		echo '
		<div class="post">
			<div class="grey_post">
			<h2><img src="http://'.$domaine.'/images/icones/search_result.png" class="icone" />'.$search_results.'<span class="right" style="font-size:70%;padding-top:5px">'.$num_rows_result.''; ?> <?php echo $results; ?><?php if($num_rows_result >'1'){echo"s";}
			echo
			'</span></h2>';
			if ($num_rows_quote >= '1' AND $num_rows_members >= '1')
				{
				echo '<h2><a href="#quotes"><img src="http://'.$domaine.'/images/icones/profil.png" class="icone">'.$quotes.'</a><span class="right"><a href="#members"><img src="http://'.$domaine.'/images/icones/staff.png" class="icone">'.$members.'</span></a></h2>';
				}
			echo '</div>';
		echo '</div>';
		
		// RESULTAT DES QUOTES
		if ($num_rows_quote >= '1')
			{
			echo '
			<div class="post" id="quotes">
			<h2><img src="http://www.teen-quotes.com/images/icones/profil.png" class="icone">'.$quotes.'<span class="right" style="font-size:90%;padding-top:5px">'.$num_rows_quote.''; echo ' '.$results.''; if($num_rows_quote >'1'){echo"s";} if ($num_rows_quote > '15'){echo ' '.$max_result.'';} echo '</span></h2>';
			
			$reponse = mysql_query("SELECT * FROM teen_quotes_quotes WHERE approved = '1' AND texte_english like '%".$value_search."%' ORDER BY id DESC LIMIT 0,15");
			
			while ($result = mysql_fetch_array($reponse))
				{
				$logged = $_SESSION['logged'];
				$id_quote = $result['id'];
				$txt_quote = $result['texte_english'];
				$auteur_id = $result['auteur_id'];
				$auteur = $result['auteur']; 
				$date_quote = $result['date'];

				$nombre_commentaires= mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_comments WHERE id_quote = '".$id_quote."'")); 
				if ($logged)
					{
					$is_favorite = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_favorite WHERE id_quote = '".$id_quote."' AND id_user = '".$id."'"));
					}
				?>
				<div class="grey_post">
				<?php echo $txt_quote; ?><br>
					<div style="font-size:65%">
						<a href="quote-<?php echo $result['id']; ?>">#<?php echo $result['id']; ?> - <?php afficher_nb_comments ($nombre_commentaires, $comments, $comment, $no_comments); ?></a><?php afficher_favori_m($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['id']); date_et_auteur_m($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
					</div>
				</div>
				<?php 
				}
			echo '</div>';
			}
		// RESULTAT DES MEMBRES
		if ($num_rows_members >= '1')
			{
			echo '
			<div class="post" id="members">
			<h2><img src="http://www.teen-quotes.com/images/icones/staff.png" class="icone">'.$members.'<span class="right" style="font-size:90%;padding-top:5px">'.$num_rows_members.''; echo ' '.$results.''; if($num_rows_members >'1'){echo"s";} if ($num_rows_members > '15'){echo ' '.$max_result.'';} echo '</span></h2>';
			
			$reponse = mysql_query("SELECT * FROM teen_quotes_account WHERE username like '%".$value_search."%'  AND hide_profile = '0' ORDER BY username ASC LIMIT 0,15");
			while ($result = mysql_fetch_array($reponse))
				{
				$id_user = $result['id'];
				$avatar = $result['avatar'];
				$username_member = $result['username'];
				$about_me = $result['about_me'];
				$country = $result['country'];
				$city = $result['city'];
				
				$nb_quotes_approved= mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE auteur_id = '".$id_user."' AND approved = '1'"));
				$nb_quotes_submited= mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE auteur_id = '".$id_user."'"));
				$nb_favorite_quotes= mysql_num_rows(mysql_query("SELECT DISTINCT id_quote FROM teen_quotes_favorite WHERE id_user = '".$id_user."'"));
				$nb_comments= mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_comments WHERE auteur_id = '".$id_user."'"));
				$nb_quotes_added_to_favorite = mysql_num_rows(mysql_query("SELECT F.id FROM teen_quotes_favorite F, teen_quotes_quotes Q WHERE F.id_quote = Q.id AND Q.auteur_id = '".$id_user."'"));
				
			
				echo '<div class="grey_post">';
				echo '<img src="http://'.$domaine.'/images/avatar/'.$avatar.'" class="user_avatar_members" /><a href="user-'.$id_user.'"><h2>'.$username_member.'';
				if (!empty($city)) 
					{
					echo '<span class="right">'.$city.'';
					}
				if (!empty($country))
					{
					if (!empty($city))
						{
						echo ' - ';
						}
					echo ''.$country.'</span>';
					}
				echo '</h2></a>';
				if (!empty($about_me)) 
					{
					echo ''.$about_me.'';
					echo '<div class="grey_line"></div>';
					}
				echo '
				<span class="bleu">'.$fav_quote.' :</span> '.$nb_favorite_quotes.'<br>
				<span class="bleu">'.$number_comments.' :</span> '.$nb_comments.'<br>
				<span class="bleu">'.$number_quotes.' :</span> '.$nb_quotes_approved.' '.$validees.' '.$nb_quotes_submited.' '.$soumises.'<br>';
				if ($nb_quotes_approved > '0')
				{
				echo '
				<span class="bleu">'.$added_on_favorites.' :</span> '.$nb_quotes_added_to_favorite.'<br>
				';
				}
				echo '</div>';
				
				$j++;
				}
			echo '</div>';
			}
		}
		// AFFICHAGE SI 0 RESULTAT
		else
		{ 
		echo '
		<div class="post">
		<h2><img src="http://www.teen-quotes.com/images/icones/search_result.png" class="icone" />'.$no_result.'</h2>
		'.$no_result_fun.'
		</div>';
		}
	}
	
include "footer.php"; ?>
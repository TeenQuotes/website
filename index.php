<?php 
include "header.php";
// CALCUL DU NOMBRE DE PAGES
$i = '0';

$retour = mysql_query("SELECT COUNT(*) AS nb_messages FROM teen_quotes_quotes WHERE approved='1'");

$donnees = mysql_fetch_array($retour);
$totalDesMessages = $donnees['nb_messages'];

$nombreDeMessagesParPage = 15; 
$nombreDePages  = ceil($totalDesMessages / $nombreDeMessagesParPage);
if (isset($_GET['p']))
	{
    $page = mysql_real_escape_string($_GET['p']);
	}
else 
	{
    $page = 1; 
	}

if ($page > $nombreDePages) 
	{
	$page = $nombreDePages;
	}

$page2 = $page + 1;
$page3 = $page - 1;

if ($page > 1)
	{
	echo '<span class="page"><a href="?p='.$page3.'">'.$previous_page.'</a> || ';
	}
if ($page == 1)
	{
	echo '<span class="page">';
	}
echo '<a href="?p='.$page2.'">'.$next_page.'</a></span><br>';


$premierMessageAafficher = ($page - 1) * $nombreDeMessagesParPage;

$reponse = mysql_query("SELECT * FROM teen_quotes_quotes WHERE approved='1' ORDER BY id DESC LIMIT $premierMessageAafficher ,  $nombreDeMessagesParPage");
while ($result = mysql_fetch_array($reponse))
	{
	$logged = $_SESSION['logged'];
	$id_quote = $result['id'];
	$txt_quote = $result['texte_english'];
	$auteur_id = $result['auteur_id'];
	$auteur = $result['auteur']; 
	$date_quote = $result['date'];

	$nombre_commentaires= mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_comments WHERE id_quote='$id_quote'")); 
	$is_favorite = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_favorite WHERE id_quote='$id_quote' AND id_user='$id'"));
?>
	<div class="post<?php if($i=='0'){echo ' no_rounded_borders_right_top';} ?>">
	<?php is_quote_new($date_quote,$last_visit,$page,$i); ?><?php echo $txt_quote; ?><br>
	<div class="footer_quote">
	<a href="quote-<?php echo $result['id']; ?>">#<?php echo $result['id']; ?> - <?php if($nombre_commentaires >'1'){echo "$nombre_commentaires $comments";}elseif($nombre_commentaires=='1'){echo "$nombre_commentaires $comment";}else{echo"$no_comments";} ?></a><?php afficher_favori($id_quote,$is_favorite,$logged,$add_favorite,$unfavorite,$_SESSION['account']); date_et_auteur ($auteur_id,$auteur,$date_quote,$on,$by,$view_his_profile); ?>
	</div>
	<?php share_fb_twitter ($id_quote,$txt_quote,$share); ?> 
	</div>
<?php 
	$i++;
	}
if ($page > 1)
	{
	if ($page >= 5)
		{
		echo '<span class="page_bottom_number"><a href="?p=1">1</a></span> <span class="left" style="margin-left:5px;margin-top:-13px">...</span>';
		for ($num_page = $page-2;$num_page < $page;$num_page++)
			{
			echo '<span class="page_bottom_number"><a href="?p='.$num_page.'">'.$num_page.'</a></span>'; 
			}
		}
	else 
		{
		for ($num_page = '1';$num_page <= $page-1;$num_page++)
			{
			echo '<span class="page_bottom_number"><a href="?p='.$num_page.'">'.$num_page.'</a></span>'; 
			}
		}
	}

if ($page <= $nombreDePages-4)
	{
	for ($num_page = $page;$num_page <= $page+2;$num_page++)
		{
		if ($num_page == $page)
			{
			echo '<span class="page_bottom_number_active"><a href="?p='.$num_page.'">'.$num_page.'</a></span>';
			}
		else
			{
			echo '<span class="page_bottom_number"><a href="?p='.$num_page.'">'.$num_page.'</a></span>';
			}
		}
	echo '<span class="left" style="margin-left:5px;margin-top:-13px">...</span>';
	echo '<span class="page_bottom_number"><a href="?p='.$nombreDePages.'">'.$nombreDePages.'</a></span>';
	}
else
	{
	for ($num_page = $page;$num_page <= $nombreDePages;$num_page++)
		{
		if ($num_page == $page)
			{
			echo '<span class="page_bottom_number_active"><a href="?p='.$num_page.'">'.$num_page.'</a></span>';
			}
		else
			{
			echo '<span class="page_bottom_number"><a href="?p='.$num_page.'">'.$num_page.'</a></span>';
			}
		}
	}
	
if ($page > 1)
	{
	echo '<span class="page_bottom"><a href="?p='.$page3.'">'.$previous_page.'</a> || ';
	}
if ($page == 1)
	{
	echo '<span class="page_bottom">';
	}
echo '<a href="?p='.$page2.'">'.$next_page.'</a></span><br>';


include "footer.php"; 
?>
<?php
function domaine()
{
	// Grant access to these variables
	global $domain, $name_website;

	return array($domain, $name_website);
}

// Used for SQL balacing
require 'database.php';

if (isset($_GET['co'])) 
{
	$data = domaine();
	$domain = $data[0];
	$name_website = $data[1];

	$pseudo = $_SESSION['username'];		
	$passwd = $_SESSION['passwd'];

	setcookie("Pseudo", $pseudo, time() + (((3600*24)*30)*12), null, '.'.$domain, false, true);
	setcookie("Pass", $passwd, time() + (((3600*24)*30)*12), null, '.'.$domain, false, true);

	unset($_SESSION['passwd']);
	// redirection
	?>
	<script language="JavaScript">
	<!--
	window.location.href="../"
	//-->
	</script>
	<?php
}

if (isset($_GET['deconnexion']))
{
	deconnexion();
}

function deconnexion()
{
	global $download_app;
	
	$data = domaine();
	$domain = $data[0];
	$name_website = $data[1];

	if (($_SESSION['security_level'] >= 2 OR $download_app) AND !isUrlMobile())
		$link = '../apps?action=disconnect';
	elseif (!isUrlMobile())	
		$link = '../apps?action=mobile';
	else
		$link = '../';

	// Destroy the session
	$_SESSION = array();
	session_destroy();
	// Delete the cookies
	setcookie("Pseudo", "Yo", time()-4200, null, '.'.$domain, false, true);
	setcookie("Pass", "Yo", time()-4200, null, '.'.$domain, false, true);
	setcookie("PHPSESSID", "Yo", time()-4200, null, '.'.$domain, false, true);
	setcookie("Pass", "Yo", time()-4200);
	setcookie("PHPSESSID", "Yo", time()-4200);
	setcookie("Pseudo", "Yo", time()-4200);

	$pseudo   = null;
	$id       = null;
	$email    = null;
	$username = null;

	?>
	<script language="JavaScript">
	<!--
	window.location.href=<?php echo '"'.$link.'"';?>
	//-->
	</script>
	<?php 
}
	
if (isset($_GET['hide_download_app'])) 
{
	$_SESSION['hide_download_app'] = true;
	?>
	<script language="JavaScript">
	<!--
	window.location.href="../"
	//-->
	</script>
	<?php
}

if (isset($_GET['show_download_app'])) 
{
	$_SESSION['hide_download_app'] = false;
	?>
	<script language="JavaScript">
	<!--
	window.location.href="../"
	//-->
	</script>
	<?php
}

function caracteresAleatoires($nombreDeCaracteres)
{
	$string = ''; 
	$chaine = "abcdefghijklmnpqrstuvwxyz123456789"; 
	srand((double)microtime()*1000000);

	for ($i = 0; $i < $nombreDeCaracteres; $i++)
		$string .= $chaine[rand()%strlen($chaine)];

	return $string;
}

function microtime_float() 
{ 
	list($usec, $sec) = explode(" ", microtime());

	return ((float)$usec + (float)$sec); 
}

$time_start = microtime_float(); 

function number_space ($number) 
{
	// Round and spaces on thousands
	$number_space = number_format($number, 0, ',', ' ');

	return $number_space;
}

function captchaMath()
{
	$n1 = mt_rand(1,84);

	if (in_array($n1, array('1', '2', '3', '6', '7', '14', '21', '42')))
	{
		$n2 = 42 / $n1;
		$phrase = ''.$n1.' x '.$n2;
	}
	else
	{
		if ($n1 <= 42)
		{
			$n2 = 42 - $n1;
			$phrase = ''.$n1.' + '.$n2;
		}
		else
		{
			$n2 = $n1 - 42;
			$phrase = ''.$n1.' - '.$n2;
		}
	}

	return array('42', $phrase);	
}

function captcha()
{
	list($resultat, $phrase) = captchaMath();
	$_SESSION['captcha'] = $resultat;
	
	return $phrase;
}

function days_between_dates($date, $ref=null)
{
	if (is_null($ref))
		$ref = time();
	else
		$ref = strtotime($ref);

	$your_date = strtotime($date);
	$datediff = abs($ref - $your_date);

	return floor($datediff / (60*60*24));
}

// Update statistics.
// Store the text in a database
// This function is called by a cron task - see /cron.php, code = updatestats
function update_stats($language) 
{
	// Grant access to these variables
	global $domain_en, $domain_fr;

	$data         = domaine();
	$domain       = $data[0];
	$name_website = $data[1];

	require 'lang/'.$language.'/statistics.php';

	$query_quote = mysql_query("SELECT COUNT(id) AS total FROM teen_quotes_quotes GROUP BY approved ORDER BY approved ASC ");

	$i = 0;
	$total_quotes = 0;
	$array_approved_quotes = array (
		"quotes_rejected" 	=> null,
		"quotes_pending" 	=> null,
		"quotes_approved" 	=> null, 
		"quotes_queued" 	=> null,
		"total_quotes" 		=> null);

	// Store the name of keys
	$keys = array_keys($array_approved_quotes);

	while ($data = mysql_fetch_array($query_quote))
	{
		$array_approved_quotes[$keys[$i]] = $data['total'];

		$i++;
		$array_approved_quotes['total_quotes'] += $data['total'];
	}

	$total_members = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account"));
	$nb_empty_avatar = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_account WHERE avatar = 'icon50.png'"));
	$nb_members_empty_profile = $total_members - $nb_empty_avatar;

	$nb_favorite = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_favorite"));
	$nb_members_has_favorite_quotes = mysql_num_rows(mysql_query("SELECT DISTINCT A.id FROM teen_quotes_account A, teen_quotes_favorite F WHERE A.id IN (F.id_user)"));
	$nb_members_no_favorite_quotes = $total_members - $nb_members_has_favorite_quotes;
	$nb_newsletter_query = mysql_query("SELECT COUNT(id) AS tot, type FROM newsletters GROUP BY type ASC");
	while ($data = mysql_fetch_array($nb_newsletter_query))
	{
		$nb_newsletter[$data['type']] = $data['tot'];
		$nb_newsletter['tot'] += $nb_newsletter[$data['type']];
	}

	$query_hide_profile = mysql_query("SELECT COUNT( id ) nb, hide_profile FROM teen_quotes_account GROUP BY hide_profile");
	$hide_profile_array = array();
	while ($data = mysql_fetch_array($query_hide_profile))
	{
		$hide_profile_array[$data['hide_profile']] = $data['nb'];
	}
	

	$query_location_signup = mysql_query("SELECT COUNT(*) as tot, location_signup FROM teen_quotes_account GROUP BY location_signup ORDER BY tot DESC");

	$query_top_user_favorite = mysql_query("SELECT COUNT( F.id ) AS nb_fav , A.id, A.username AS username FROM teen_quotes_favorite F, teen_quotes_quotes Q, teen_quotes_account A WHERE F.id_quote = Q.id AND Q.auteur_id = A.id GROUP BY A.id ORDER BY COUNT( F.id ) DESC LIMIT 0,20");
	$query_search = mysql_query("SELECT * FROM teen_quotes_search ORDER BY value DESC LIMIT 0,20");
	$nb_search_query = mysql_fetch_array(mysql_query("SELECT SUM(value) AS nb_search FROM teen_quotes_search"));
	$nb_search = $nb_search_query['nb_search'];
	$graph_stats_js = "
	function graph_quotes() 
	{
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Quote');
		data.addColumn('number', 'Status');
		data.addRows(4);
		data.setValue(0, 0, '".$approved."');
		data.setValue(0, 1, ".$array_approved_quotes['quotes_approved'].");
		data.setValue(1, 0, '".$rejected."');
		data.setValue(1, 1, ".$array_approved_quotes['quotes_rejected'].");
		data.setValue(2, 0, '".$pending."');
		data.setValue(2, 1, ".$array_approved_quotes['quotes_pending'].");
		data.setValue(3, 0, '".$waiting_to_be_posted."');
		data.setValue(3, 1, ".$array_approved_quotes['quotes_queued'].");

		var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0
		});
		formatter.format(data, 1);

		new google.visualization.PieChart(document.getElementById('graph_quotes')).
		draw(data, {title:'".$total_nb_quotes." : ".number_space($array_approved_quotes['total_quotes'])."'});
	}

	function graph_empty_profile()
	{
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Profile');
		data.addColumn('number', 'Number');
		data.addRows(2);
		data.setValue(0, 0, '".$avatar_not_fullfilled."');
		data.setValue(0, 1, ".$nb_empty_avatar.");
		data.setValue(1, 0, '".$avatar_fullfilled."');
		data.setValue(1, 1, ".$nb_members_empty_profile.");

		new google.visualization.PieChart(document.getElementById('graph_empty_profile')).
		draw(data, {title:'".$total_nb_members." : ".number_space($total_members)."'});
	}

	function graph_hide_profile()
	{
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Profile');
		data.addColumn('number', 'Number');
		data.addRows(2);
		data.setValue(0, 0, '".$public_profile."');
		data.setValue(0, 1, ".$hide_profile_array[0].");
		data.setValue(1, 0, '".$private_profile."');
		data.setValue(1, 1, ".$hide_profile_array[1].");

		var options = {
			title: '".$privacy_and_users."'
		};

		var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0
		});
		formatter.format(data, 1);

		new google.visualization.PieChart(document.getElementById('graph_hide_profile')).
		draw(data, options);
	}

	function graph_newsletter() 
	{
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Profile');
		data.addColumn('number', 'Status');
		data.addRows(2);
		data.setValue(0, 0, '".$daily_newsletter." : ".$nb_newsletter['daily']."');
		data.setValue(0, 1, ".$nb_newsletter['daily'].");
		data.setValue(1, 0, '".$weekly_newsletter." : ".$nb_newsletter['weekly']."');
		data.setValue(1, 1, ".$nb_newsletter['weekly'].");

		new google.visualization.PieChart(document.getElementById('graph_newsletter')).
		draw(data, {title:'".$people_subscribed_newsletter." : ".number_space($nb_newsletter['tot'])."'});
	}

	function members_favorite_quote() 
	{
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Profile');
		data.addColumn('number', 'Status');
		data.addRows(2);
		data.setValue(0, 0, '".$members_with_fav_quotes." : ".$nb_members_has_favorite_quotes."');
		data.setValue(0, 1, ".$nb_members_has_favorite_quotes.");
		data.setValue(1, 0, '".$members_without_fav_quotes." : ".$nb_members_no_favorite_quotes."');
		data.setValue(1, 1, ".$nb_members_no_favorite_quotes.");

		new google.visualization.PieChart(document.getElementById('members_favorite_quote')).
		draw(data, {title:'".$members_and_fav_quotes."'});
	}

	function top_user_favorite_quote()
	{
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Profile');
		data.addColumn('number', 'Status');
		data.addRows(21);";
		$i = 0;
		$sum_fav_top_user = 0;
		while ($donnees = mysql_fetch_array($query_top_user_favorite))
		{
			$nb_fav = $donnees['nb_fav'];
			$username = $donnees['username'];

			$sum_fav_top_user += $nb_fav;
			$graph_stats_js .="
			data.setValue(".$i.", 0, '".$username." : ".$nb_fav."');
			data.setValue(".$i.", 1, ".$nb_fav.");
			";
			$i++;
		}
		$reste_nb_favorite = $nb_favorite -  $sum_fav_top_user;
		$graph_stats_js .="
		data.setValue(".$i.", 0, '".$others." : ".$reste_nb_favorite."');
		data.setValue(".$i.", 1, ".$reste_nb_favorite.");

		new google.visualization.PieChart(document.getElementById('top_user_favorite_quote')).
		draw(data, {title:'".$top_members_ordered_by_nb_quotes_in_fav." (".$nb_favorite." ".$quotes_in_fav.")'});
	}

	function location_signup() 
	{
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Profile');
		data.addColumn('number', 'Status');
		data.addRows(5);";
		$i = 0;
		while($donnees = mysql_fetch_array($query_location_signup))
		{
			$location_signup_device = $donnees['location_signup'];
			$nb = $donnees['tot'];
			$graph_stats_js .= "
			data.setValue(".$i.", 0, '".$location_signup_device."');
			data.setValue(".$i.", 1, ".$nb.");
			";
			$i++;
		}
		$graph_stats_js .= "
		var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0
		});
		formatter.format(data, 1);

		new google.visualization.PieChart(document.getElementById('graph_location_signup')).
		draw(data, {title:'".$location_signup."'});
	}


	function graph_search()
	{
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Profile');
		data.addColumn('number', 'Status');
		data.addRows(21);";
		$j = 0;
		$sum_nb_search = 0;
		while ($donnees = mysql_fetch_array($query_search))
		{
			$value = $donnees['value'];
			$text = ucfirst($donnees['text']);

			$sum_nb_search += $value;

			$graph_stats_js .="
			data.setValue(".$j.", 0, '".$text." : ".$value."');
			data.setValue(".$j.", 1, ".$value.");
			";
			$j++;
		}
		$reste_nb_search = $nb_search - $sum_nb_search;
		$graph_stats_js .="
		data.setValue(".$i.", 0, '".$others." : ".$reste_nb_search."');
		data.setValue(".$i.", 1, ".$reste_nb_search.");
		var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0
		});
		formatter.format(data, 1);

		new google.visualization.PieChart(document.getElementById('graph_search')).
		draw(data, {title:'".$total_nb_search." : ".$nb_search."'});
	}

	function members_over_time()
	{
			 var data = google.visualization.arrayToDataTable([
			 	['Date', '".$number_of_members."'],";
		$timestamp = 1285884000;
		$i = 1;
		while ($timestamp < time())
		{
			$timestamp = strtotime("+1 month", $timestamp);

			if ($timestamp < time())
			{
				$query = mysql_query("SELECT COUNT(id) AS nb_members FROM teen_quotes_account WHERE UNIX_TIMESTAMP(joindate) <= '".$timestamp."'");
				$data = mysql_fetch_array($query);
				$nb_members = $data['nb_members'];
				$time_txt = date('m/y', $timestamp);
				$graph_stats_js .= "['".$time_txt."', ".$nb_members."],";
			}

			$i++;
		}

		$graph_stats_js .= " 
		]);
		var options = {
			title: '".$members_over_time."'
		};

	    var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0
		});
		formatter.format(data, 1);

	    var chart = new google.visualization.LineChart(document.getElementById('members_time'));
	    chart.draw(data, options);
    }

    function members_over_time_exp()
	{
			 var data = google.visualization.arrayToDataTable([
			 	['Date', '".$number_of_members."'],";
		$timestamp = 1285884000;
		$i = 1;
		while ($timestamp < time())
		{
			$timestamp = strtotime("+1 month", $timestamp);

			if ($timestamp < time())
			{
				$query = mysql_query("SELECT COUNT(id) AS nb_members FROM teen_quotes_account WHERE UNIX_TIMESTAMP(joindate) <= '".$timestamp."'");
				$data = mysql_fetch_array($query);
				$nb_members = $data['nb_members'];
				$time_txt = date('m/y', $timestamp);
				$graph_stats_js .= "[".$i.", ".$nb_members."],";
			}

			$i++;
		}

		$graph_stats_js .= " 
		]);
		var options = {
			title: '".$members_over_time."',
			hAxis: {title: '".$months_txt."'},
			trendlines:
			{
				0:
				{
					type: 'exponential',
					visibleInLegend: true,
					color: 'orange',
					opacity: '0.5',
					labelInLegend: '".$exponential_regression."'
				}
			}
		};

	    var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0
		});
		formatter.format(data, 1);

	    var chart = new google.visualization.LineChart(document.getElementById('members_time_exp'));
	    chart.draw(data, options);
    }

	function quotes_over_time()
	{
		var data = google.visualization.arrayToDataTable([
		['Date', '".$unapproved_quotes."', '".$approved_quotes."', '".$txt_total_quotes."'],";
		$timestamp = 1285884000;
		$i = 1;
		$array_quotes_over_time = array();

		while ($timestamp < time())
		{
			$timestamp = strtotime("+1 month", $timestamp);

			if ($timestamp < time())
			{
				$query = mysql_query("	SELECT COUNT(id) AS tot, approved
										FROM teen_quotes_quotes
										WHERE approved <>  0
										AND UNIX_TIMESTAMP(timestamp_created) <= '".$timestamp."'
										GROUP BY approved
										ORDER BY approved ASC");
				
				$nb_approved = 0;
				while ($data = mysql_fetch_array($query))
				{
					// approved = '-1'
					if ($data['approved'] == -1)
						$nb_unapproved = $data['tot'];
					// approved = '1' OR approved = '2'
					elseif ($data['approved'] == 1 OR $data['approved'] == 2)
						$nb_approved += $data['tot'];

				}
				$nb_tot = $nb_unapproved + $nb_approved;
				$time_txt = date('m/y', $timestamp);

				// Store it in an array, so we can draw percentages later
				$array_quotes_over_time[$i] = $time_txt.':'.$nb_unapproved.":".$nb_approved;

				$graph_stats_js .= "['".$time_txt."', ".$nb_unapproved.", ".$nb_approved.", ".$nb_tot."],";
			}
			$i++;
		}

		$graph_stats_js .= " 
		]);
		var options = {
			title: '".$quotes_over_time."',
			series: {0:{color:'red'}, 1:{color:'green'}, 2:{color:'blue'}},
	        };
	    var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0
		});
		formatter.format(data, 1); 
		formatter.format(data, 2); 
		formatter.format(data, 3); 

	    var chart = new google.visualization.LineChart(document.getElementById('quotes_time'));
	        chart.draw(data, options);
    }

    function quotes_over_time_percentage()
    {
		var data = google.visualization.arrayToDataTable([
		['Date', '".$unapproved_quotes."', '".$approved_quotes."', '".$txt_total_quotes."'],";

		for ($i = 1; $i <= count($array_quotes_over_time); $i++)
		{
			// Extract data from the array
			list($time_txt, $nb_unapproved, $nb_approved) = explode(":", $array_quotes_over_time[$i]);
			$nb_tot = $nb_unapproved + $nb_approved;

			$graph_stats_js .= "['".$time_txt."', ".floor($nb_unapproved/$nb_tot*100).", ".floor($nb_approved/$nb_tot*100).", 100],";
		}

		$graph_stats_js .= " 
		]);
		var options = {
	          title: '".$quotes_over_time." (%)',
	          series: {0:{color:'red'}, 1:{color:'green'}, 2:{color:'blue', areaOpacity:0.1}}
	        };

	    var formatter = new google.visualization.NumberFormat(
		{
			suffix: '%',
			fractionDigits: 0
		});
		formatter.format(data, 1);
		formatter.format(data, 2);
		formatter.format(data, 3);

	    var chart = new google.visualization.AreaChart(document.getElementById('quotes_time_percentage'));
	        chart.draw(data, options);
    }

    function comments_over_time()
    {
		var data = google.visualization.arrayToDataTable([
		['Date', '".$number_of_comments."'],";
		$timestamp = 1285884000;
		while ($timestamp < time())
		{
			$timestamp = strtotime("+1 month", $timestamp);

			if ($timestamp < time())
			{
				$query = mysql_query("	SELECT COUNT(id) AS tot
										FROM teen_quotes_comments
										WHERE UNIX_TIMESTAMP(timestamp_created) <= '".$timestamp."'");
				while ($data = mysql_fetch_array($query))
					$nb_tot = $data['tot'];

				$time_txt = date('m/y', $timestamp);

				$graph_stats_js .= "['".$time_txt."', ".$nb_tot."],";
			}
		}

		$graph_stats_js .= " 
		]);
		var options = {
	          title: '".$comments_over_time."'
	        };
	    var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0
		});
		formatter.format(data, 1);

	    var chart = new google.visualization.LineChart(document.getElementById('graph_comments_time'));
	        chart.draw(data, options);
    }

    function comments_over_time_exp()
    {
		var data = google.visualization.arrayToDataTable([
		['Date', '".$number_of_comments."'],";
		$timestamp = 1285884000;
		$i = 1;
		while ($timestamp < time())
		{
			$timestamp = strtotime("+1 month", $timestamp);

			if ($timestamp < time())
			{
				$query = mysql_query("	SELECT COUNT(id) AS tot
										FROM teen_quotes_comments
										WHERE UNIX_TIMESTAMP(timestamp_created) <= '".$timestamp."'");
				while ($data = mysql_fetch_array($query))
					$nb_tot = $data['tot'];

				$time_txt = date('m/y', $timestamp);

				$graph_stats_js .= "[".$i.", ".$nb_tot."],";
			}

			$i++;
		}

		$graph_stats_js .= " 
		]);
		var options = {
	        title: '".$comments_over_time."',
	        hAxis: {title: '".$months_txt."'},
			trendlines:
			{
				0:
				{
					type: 'exponential',
					visibleInLegend: true,
					color: 'orange',
					opacity: '0.5',
					labelInLegend: '".$exponential_regression."'
				}
			}
	        };
	    var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0
		});
		formatter.format(data, 1);

	    var chart = new google.visualization.LineChart(document.getElementById('graph_comments_time_exp'));
	        chart.draw(data, options);
    }";

    // Fetching age data for users
    $query = mysql_query("SELECT birth_date FROM teen_quotes_account WHERE birth_date <> ''");
	$age = array();

	while ($data = mysql_fetch_array($query))
	{
		$username = $data['username'];
		$ageValue = age($data['birth_date']);

		// Delete strange values
		if ($ageValue >= 5 AND $ageValue <= 80)
			$age[$ageValue]++;
	}
	// Sort arrays (ascending sort, by keys)
	ksort($age);

    $graph_stats_js .= "
    function users_ages()
    {
        var data = google.visualization.arrayToDataTable([
          ['".$age_legend."', '".$nb_of_users."'],";
          foreach($age as $key => $value)
		  {
			$graph_stats_js .= '['.$key.', '.$value.'],';
		  }
	$graph_stats_js .= "
        ]);

        var options = {
          title: '".$age_of_users."',
          hAxis: {title: '".$age_of_users."'}
        };

        var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0,
			suffix: ' / ".array_sum($age)." ".$users_txt."'
		});
		formatter.format(data, 1);

        var chart = new google.visualization.ColumnChart(document.getElementById('users_ages'));
        chart.draw(data, options);

        var table = new google.visualization.Table(document.getElementById('users_ages_table'));
        table.draw(data, options);
    }";

    // Fetching comments length
    $query = mysql_query("SELECT COUNT( * ) AS nb, LENGTH(texte) AS length FROM teen_quotes_comments GROUP BY LENGTH(texte) ORDER BY  `nb` DESC ");
	$comment_length_array = array();

	while ($data = mysql_fetch_array($query))
	{
		$comment_length_array[$data['length']] = $data['nb'];
	}

	$graph_stats_js .= "
    function comments_length()
    {
        var data = google.visualization.arrayToDataTable([
          ['".$comment_length."', '".$nb_comments."'],";
          foreach($comment_length_array as $key => $value)
		  {
			$graph_stats_js .= '['.$key.', '.$value.'],';
		  }
	$graph_stats_js .= "
        ]);

        var options = {
          title: '".$comments_length."',
          hAxis: {title: '".$comment_length."'}
        };

        var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0,
			suffix: ' / ".array_sum($comment_length_array)."'
		});
		formatter.format(data, 1);

        var chart = new google.visualization.ColumnChart(document.getElementById('comments_length'));
        chart.draw(data, options);

        var table = new google.visualization.Table(document.getElementById('comments_length_table'));
        table.draw(data, options);
    }";

    // Fetching sign up methods
    $query = mysql_query("SELECT param, value FROM  `teen_quotes_settings` WHERE param LIKE 'signup_%'");
    
    $graph_stats_js .= "
    function sign_up_methods()
    {
    	var data = google.visualization.arrayToDataTable([
    	    ['".$sign_up_method."', '".$number_txt."'],";
    	while ($data = mysql_fetch_array($query))
    	{
    		$graph_stats_js .= '[\''.${$data['param']}.'\', '.$data['value'].'],';
    	}
    $graph_stats_js .= "
        ]);

        var options = {
          title: '".$sign_up_methods."'
        };

        var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0,
		});
		formatter.format(data, 1);

        var chart = new google.visualization.PieChart(document.getElementById('sign_up_methods'));
        chart.draw(data, options);
    }";

    $array_days_between_signup_post_quote = array();
	$array_nb_quotes_submitted = array();

	// Find every users that have already submitted a quote
	$users = mysql_query("SELECT id, joindate FROM teen_quotes_account WHERE id IN (SELECT DISTINCT auteur_id FROM teen_quotes_quotes)");
	while ($data = mysql_fetch_array($users)) 
	{
		$joindate = $data['joindate'];
		$id       = $data['id'];

		// Count days between signup and first quote submission
		$query_quote_min = mysql_query("SELECT timestamp_created FROM teen_quotes_quotes WHERE auteur_id = ".$id." ORDER BY id ASC LIMIT 0,1");
		$data = mysql_fetch_array($query_quote_min);
		$timestamp_created = $data['timestamp_created'];

		$days = days_between_dates($joindate, $timestamp_created);
		$array_days_between_signup_post_quote[$days]++;

		// Count quotes submitted by each users
		$query_count_quote = mysql_query("SELECT COUNT(id) as count_quote FROM teen_quotes_quotes WHERE auteur_id = ".$id."");
		$data = mysql_fetch_array($query_count_quote);
		$count_quote = $data['count_quote'];
		
		$array_nb_quotes_submitted[$count_quote]++;

	}
	// Sort arrays (ascending sort, by keys)
	ksort($array_days_between_signup_post_quote);
	ksort($array_nb_quotes_submitted);

	$sum_value = 0;

	$graph_stats_js .= "
    function days_between_signup_post_quote()
    {
        var data = google.visualization.arrayToDataTable([
          ['".$days_between_signup_post_quote."', '".$nb_users_txt."'],";
          foreach($array_days_between_signup_post_quote as $key => $value)
		  {
			// Do not count strange data
			if ($value >= 5)
			{
				$graph_stats_js .= '[\''.$key.'\', '.$value.'],';
				$sum_value += $value;
			}
		  }
	$graph_stats_js .= "
        ]);

        var options = {
          title: '".$days_between_signup_post_quote."',
          hAxis: {title: '".$days_between_signup_post_quote."'}
        };

        var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0,
			suffix: ' / ".number_space($sum_value)." ".$users_txt."'
		});
		formatter.format(data, 1);

        var chart = new google.visualization.ColumnChart(document.getElementById('days_between_signup_post_quote'));
        chart.draw(data, options);

        var table = new google.visualization.Table(document.getElementById('days_between_signup_post_quote_table'));
        table.draw(data, options);
    }";

    // Reset
    $sum_value = 0;

    $graph_stats_js .= "
    function nb_quotes_submitted_user()
    {
        var data = google.visualization.arrayToDataTable([
          ['".$nb_quotes_submitted_user."', '".$nb_users_txt."'],";
          foreach($array_nb_quotes_submitted as $key => $value)
		  {
			// Do not count strange data
			if ($value >= 1)
			{
				$graph_stats_js .= '[\''.$key.'\', '.$value.'],';
				$sum_value += $value;
			}
		  }
	$graph_stats_js .= "
        ]);

        var options = {
          title: '".$nb_quotes_submitted_user."',
          hAxis: {title: '".$nb_quotes_submitted_user."'}
        };

        var formatter = new google.visualization.NumberFormat(
		{
			groupingSymbol: ' ',
			fractionDigits: 0,
			suffix: ' / ".number_space($sum_value)." ".$users_txt."'
		});
		formatter.format(data, 1);

        var chart = new google.visualization.ColumnChart(document.getElementById('nb_quotes_submitted_user'));
        chart.draw(data, options);

        var table = new google.visualization.Table(document.getElementById('nb_quotes_submitted_user_table'));
        table.draw(data, options);
    }";

    $graph_stats_js .= "
    google.setOnLoadCallback(comments_over_time);
    google.setOnLoadCallback(comments_over_time_exp);
	google.setOnLoadCallback(graph_quotes);
	google.setOnLoadCallback(graph_empty_profile); 
	google.setOnLoadCallback(graph_hide_profile); 
	google.setOnLoadCallback(graph_newsletter); 
	google.setOnLoadCallback(members_favorite_quote); 
	google.setOnLoadCallback(top_user_favorite_quote);
	google.setOnLoadCallback(graph_search);
	google.setOnLoadCallback(location_signup);
	google.setOnLoadCallback(members_over_time);
	google.setOnLoadCallback(members_over_time_exp);
	google.setOnLoadCallback(quotes_over_time);
	google.setOnLoadCallback(quotes_over_time_percentage);
	google.setOnLoadCallback(users_ages);
	google.setOnLoadCallback(comments_length);
	google.setOnLoadCallback(sign_up_methods);
	google.setOnLoadCallback(days_between_signup_post_quote);
	google.setOnLoadCallback(nb_quotes_submitted_user);";

	// Store it in the database
	$query = mysql_query("UPDATE stats SET text_js_".$language." = '".mysql_real_escape_string($graph_stats_js)."' WHERE id = 1");
}

function display_stats($language=null) 
{
	// Default language value
	if (is_null($language))
		$language = 'english';

	$query = mysql_query("SELECT text_js_".$language." as js_stats, timestamp FROM stats WHERE id = 1");
	$data = mysql_fetch_array($query);

	echo "<script type=\"text/javascript\">".$data['js_stats']."</script>";

	// Return the ISO 8601 timestamp
	return date('c', strtotime($data['timestamp']));
}

function last_visit($session_last_visit, $last_visit, $id_account)
{
	if ($session_last_visit != '1')
	{
		$today = date("d/m/Y");
		if ($last_visit != $today)
		{
			$update_last_visit = mysql_query("UPDATE teen_quotes_account SET last_visit = '$today' WHERE id = '$id_account'");
			$_SESSION['last_visit_user'] = '1';
		}
	}
}

function age($naiss)  
{
	$jour  = substr($naiss, 0, 2);
	$mois  = substr($naiss, 3, 2);
	$annee = substr($naiss, 6, 4);

	$today['mois']  = date('n');
	$today['jour']  = date('j');
	$today['annee'] = date('Y');
	$annees = $today['annee'] - $annee;

	if ($today['mois'] <= $mois) 
	{
		if ($mois == $today['mois']) 
		{
			if ($jour > $today['jour'])
				$annees--;	
		}
		else
			$annees--;
	}

	return $annees;
}

function date_est_valide($date)
{
	if (preg_match("#[0-9]{2}[\/][0-9]{2}[\/][0-9]{4}#", $date))
	{
		$jour  = substr($date, 0, 2);
		$mois  = substr($date, 3, 2);
		$annee = substr($date, 6, 4);

		return checkdate($mois, $jour, $annee);
	}
	else	
		return false;
}

function usernameIsValid($username)
{
	return (preg_match("#^[a-z0-9_]+$#", $username));
}
  
function display_page_bottom($page, $nombreDePages, $nom_lien_page, $div_redirection, $previous_page, $next_page, $index = false)
{
	$nb_next_page     = $page + 1;
	$nb_previous_page = $page - 1;

	if ($index)
	{
		$margin_middle = '-7px;color:#CCC';
		$margin_index = ' no_margin_left';
		$margin_index_right = ' no_margin_right';
	}
	else
	{
		$margin_middle = '-13px';
		$margin_index = '';
		$margin_index_right = '';
	}

	if (isUrlMobile())
	{
		$ecart_page = 1;
		$gap_txt = '.';
	}
	else
	{
		$ecart_page = 2;
		$gap_txt = '...';
	}

	if ($page > 1)
	{
		if ($page >= 5)
		{
			echo '<span class="page_bottom_number'.$margin_index.'"><a href="?'.$nom_lien_page.'=1">1</a></span> <span class="gap_page" style="margin-top:'.$margin_middle.'">'.$gap_txt.'</span>';
			
			for ($num_page = $page-$ecart_page;$num_page < $page;$num_page++)
				echo '<span class="page_bottom_number"><a href="?'.$nom_lien_page.'='.$num_page.$div_redirection.'">'.$num_page.'</a></span>'; 
		}
		else 
		{
			for ($num_page = 1;$num_page <= $page-1;$num_page++)
			{
				if ($index == true)
					$margin_index = ($num_page == '1') ?  ' no_margin_left' : '';
				echo '<span class="page_bottom_number'.$margin_index.'"><a href="?'.$nom_lien_page.'='.$num_page.$div_redirection.'">'.$num_page.'</a></span>'; 
			}
		}
	}

	if ($page <= $nombreDePages-4)
	{
		for ($num_page = $page;$num_page <= $page+$ecart_page;$num_page++)
		{
			if ($num_page == $page)
			{
				if ($index == true)
					$margin_index = ($num_page == '1') ?  ' no_margin_left' : '';

				echo '<span class="page_bottom_number_active '.$margin_index.'"><a href="?'.$nom_lien_page.'='.$num_page.$div_redirection.'">'.$num_page.'</a></span>';
			}
			else
				echo '<span class="page_bottom_number"><a href="?'.$nom_lien_page.'='.$num_page.$div_redirection.'">'.$num_page.'</a></span>';
		}

		echo '<span class="gap_page" style="margin-top:'.$margin_middle.'">'.$gap_txt.'</span>';
		echo '<span class="page_bottom_number"><a href="?'.$nom_lien_page.'='.$nombreDePages.$div_redirection.'">'.$nombreDePages.'</a></span>';
	}
	else
	{
		for ($num_page = $page;$num_page <= $nombreDePages;$num_page++)
		{
			if ($num_page == $page)
				echo '<span class="page_bottom_number_active'.$margin_index.'"><a href="?'.$nom_lien_page.'='.$num_page.$div_redirection.'">'.$num_page.'</a></span>';
			else
				echo '<span class="page_bottom_number'.$margin_index.'"><a href="?'.$nom_lien_page.'='.$num_page.$div_redirection.'">'.$num_page.'</a></span>';
		}
	}

	
	if ($page < $nombreDePages)
	{
		echo '<span class="page_bottom'.$margin_index_right.'"><a href="?'.$nom_lien_page.'='.$nb_next_page.$div_redirection.'" title="'.$next_page.'">'.$next_page.'</a></span>';
		$margin_done = true;
	}
	if ($page > 1)
	{
		if ($margin_done == true)
			$margin_index_right = '';

		echo '<span class="page_bottom"><a href="?'.$nom_lien_page.'='.$nb_previous_page.$div_redirection.'" title="'.$previous_page.'">'.$previous_page.'</a></span>';
	}

	echo '<div class="clear"></div>';
}
	
function display_page_top($nb_messages, $nb_messages_par_page, $lien, $previous_page, $next_page, $div_redirection = null, $margin = false)
{
	$nombreDePages = floor($nb_messages / $nb_messages_par_page);

	if ($nombreDePages <= 0)
		$nombreDePages = 1;
	
	if (isset($_GET[$lien]))
		$page = mysql_real_escape_string($_GET[$lien]);
	else 
		$page = 1; 

	if ($page > $nombreDePages) 
		$page = $nombreDePages;

	$nb_next_page     = $page + 1;
	$nb_previous_page = $page - 1;

	// Special margins
	$page_index = '';
	if ($margin)
	{
		$margin_page = ' page_index';
		$margin_right = ' no_margin_right';
	}

	if ($page < $nombreDePages)
	{
		echo '<span class="page'.$margin_page.$margin_right.'"><a href="?'.$lien.'='.$nb_next_page.$div_redirection.'" title="'.$next_page.'">'.$next_page.'</a></span>';
		$margin_done = true;
	}

	if ($page > 1)
	{
		// If the margin was already set, do no add a margin again
		if ($margin_done)
			$margin_right = '';
		echo '<span class="page'.$margin_page.$margin_right.'"><a href="?'.$lien.'='.$nb_previous_page.$div_redirection.'" title="'.$previous_page.'">'.$previous_page.'</a></span>';
	}
	if ($nombreDePages != 1)
		echo '<br/>';

	$premierMessageAafficher = ($page - 1) * $nb_messages_par_page;

	return array($premierMessageAafficher, $nombreDePages, $page);
}
	
function is_quote_new($date_quote, $last_visit, $page, $compteur_quote)
{
	include "config.php";

	$yesterday_timestamp = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
	$yesterday = date("d/m/Y", $yesterday_timestamp);

	$jour  = substr($date_quote, 0, 2);
	$mois  = substr($date_quote, 3, 2);
	$annee = substr($date_quote, 6, 4);
	$timestamp_date_quote = mktime(0, 0, 0, $mois, $jour, $annee); 

	$jour_last_visit  = substr($last_visit, 0, 2);
	$mois_last_visit  = substr($last_visit, 3, 2);
	$annee_last_visit = substr($last_visit, 6, 4);
	$timestamp_last_visit = mktime(0, 0, 0, $mois_last_visit, $jour_last_visit, $annee_last_visit); 


	if ($date_quote == $yesterday OR ($timestamp_last_visit != '943916400' AND $timestamp_date_quote > $timestamp_last_visit) OR ($page == '1' AND $compteur_quote < $nb_quote_released_per_day))
		echo '<span class="icone_new_quote hide_this"></span>';
}

function display_individual_story ($data)
{
	global $tell_us_your_story, $tell_us_how_you_use, $domain, $story;

	$id_story       = $data['id_story'];
	$txt_represent  = $data['txt_represent'];
	$txt_frequence  = $data['txt_frequence'];
	$date           = date(('d/m/Y'), strtotime($data['date']));
	$username_story = $data['username'];
	$id_user_story  = $data['id_user'];
	$avatar_story   = $data['avatar'];

	echo '
	<div class="grey_post post_individual_story">
		<h2 class="blue"><a href="//stories.'.$domain.'/story/'.$id_story.'" title="'.$story.' '.$id_story.'">#'.$id_story.'</a> - <a href="//'.$domain.'/user-'.$id_user_story.'" title="'.$username.'">'.$username_story.'</a><span class="right date_story">'.$date.'</span></h2>
		<a href="//'.$domain.'/user-'.$id_user_story.'" title="'.$username.'"><img src="//'.$domain.'/images/avatar/'.$avatar_story.'" class="story_avatar avatar_individual_story fade_on_hover" alt="'.$username.'"/></a>
		<div class="story_description no_limit_height">
			<h3>'.$tell_us_your_story.'</h3>
			<div class="dark_gray light_shadow tell_story">
				'.$txt_represent.'
			</div>
			<br/>
			<h3>'.$tell_us_how_you_use.'</h3>
			<div class="dark_gray light_shadow tell_story">
				'.$txt_frequence.'
			</div>
		</div>
		<div class="clear"></div>
	</div>';
}

// TO DO : user.php && /m/user.php
function displayQuote ($result, $page, $i, $type='random')
{
	// Grant access
	global $last_visit;

	$is_mobile = isUrlMobile();

	// Variables from the array
	$id_quote            = $result['id'];
	$txt_quote           = $result['texte_english'];
	$auteur_id           = $result['auteur_id'];
	$auteur              = $result['auteur']; 
	$date_quote          = $result['date'];
	$nombre_commentaires = $result['nb_comments'];
	$logged              = $_SESSION['logged'];

	if ($logged)
		$is_favorite = $result['is_favorite'];

	// Special class for search and user
	$class_div = (in_array($type, array('search', 'user'))) ? 'grey_post' : 'post';

	// DESKTOP
	if (!$is_mobile)
	{
		echo '
		<div class="'.$class_div.'">';
			if ($type == 'index')
				is_quote_new($date_quote, $last_visit, $page, $i);
		
		echo 
			$txt_quote.'<br/>
			<div class="footer_quote">
				<a href="quote-'.$id_quote.'" title="Quote #'.$id_quote.'">#'.$id_quote; echo afficher_nb_comments ($nombre_commentaires).'</a>'; echo afficher_favori($id_quote, $is_favorite, $logged, $_SESSION['id']); echo date_et_auteur ($auteur_id, $auteur, $date_quote).'
			</div>
			'.share_fb_twitter ($id_quote, $txt_quote).'
		</div>';

	}
	// MOBILE
	else
	{
		echo '
		<div class="'.$class_div.'">
			'.$txt_quote.'<br/>
			<div class="footer_quote">
				<a href="quote-'.$id_quote.'" title="Quote #'.$id_quote.'">#'.$id_quote; echo afficher_nb_comments ($nombre_commentaires).'</a>'; echo afficher_favori($id_quote, $is_favorite, $logged); echo date_et_auteur($auteur_id, $auteur, $date_quote).'
			</div>
		</div>';
	}
}

// Publish $nb_quote_released_per_day quotes of the day
function flush_quotes ()
{
	include "config.php";

	$data = domaine();
	$domain = $data[0];
	$name_website = $data[1];

	$date_quote = date("d/m/Y");
	$date_quote_yesterday = date("d/m/Y", strtotime('-1 day'));

	$query = mysql_query("SELECT a.id_quote id_quote FROM approve_quotes a, teen_quotes_quotes q WHERE (a.quote_release LIKE '%".$date_quote."%' OR a.quote_release LIKE '%".$date_quote_yesterday."%') AND a.id_quote = q.id AND q.approved = '2' ORDER BY a.id_quote ASC");
	$affected_rows = mysql_affected_rows();

	while ($result = mysql_fetch_array($query))
	{
		$id_quote = $result['id_quote'];

		$query_texte_quote = mysql_fetch_array(mysql_query("SELECT q.texte_english texte_english,q.date date, q.auteur_id auteur_id, a.username username, a.email email FROM teen_quotes_quotes q, teen_quotes_account a WHERE q.auteur_id = a.id AND q.id = '".$id_quote."'"));
		$texte_quote  = $query_texte_quote['texte_english'];
		$date_quote   = $query_texte_quote['date'];
		$auteur_id    = $query_texte_quote['auteur_id'];
		$email_auteur = $query_texte_quote['email'];
		$name_auteur  = $query_texte_quote['username'];

		$approve_quote = mysql_query("UPDATE teen_quotes_quotes SET approved = '1' WHERE id = '".$id_quote."'");

		if ($approve_quote AND !empty($email_auteur)) 
		{
			if ($domain == $domain_en)
			{
				$message = ''.$top_mail.' Hello <font color="#394DAC"><b>'.$name_auteur.'</b></font> !<br/><br/>Your quote has been <font color="#394DAC"><b>approved</b></font> recently by a member of our team ! <div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br/><br/><a href="http://teen-quotes.com/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://teen-quotes.com/user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> on '.$date_quote.'</span></div>Congratulations !<br/><br/>Your quote is now visible on our website. You can share it or comment it if you want !<br/><br/><br/>If you want to see your quote, <a href="http://teen-quotes.com/quote-'.$id_quote.'" target="_blank">click here</a>.<br/><br/><br/>Sincerely,<br/><b>The Teen Quotes Team</b><br/><br/><br/><div style="border-top:1px dashed #CCCCCC"></div><br/><br/>VERSION FRANCAISE :<br/><br/>Bonjour <font color="#394DAC"><b>'.$name_auteur.'</b></font> !<br/><br/>Votre citation a été récemment <font color="#394DAC"><b>approuvée</b></font> par un membre de notre équipe ! <div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br/><br/><a href="http://teen-quotes.com/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">par <a href="http://teen-quotes.com/user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> le '.$date_quote.'</span></div>Congratulations !<br/><br/>Votre citation est maintenant visible sur Teen Quotes. Vous pouvez dès à présent la partager ou la commenter si vous le souhaitez !<br/><br/><br/>Si vous voulez voir votre citation, <a href="http://teen-quotes.com/quote-'.$id_quote.'" target="_blank">cliquez ici</a>.<br/><br/><br/>Cordialement,<br/><b>The Teen Quotes Team</b> '.$end_mail;
				$mail = mail($email_auteur, "Quote approved", $message, $headers);
			}
			elseif ($domain == $domain_fr)
			{
				$message = "$top_mail Bonjour <font color=\"#394DAC\"><b>$name_auteur</b></font> !<br/><br/>Votre citation a été récemment <font color=\"#394DAC\"><b>approuvée</b></font> par un membre de notre équipe ! <div style=\"background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px\">$texte_quote<br/><br/><a href=\"http://".$domain."/quote-$id_quote\" target=\"_blank\">#$id_quote</a><span style=\"float:right\">par <a href=\"http://".$domain."/user-$auteur_id\" target=\"_blank\">$name_auteur</a> le $date_quote</span></div>Congratulations !<br/><br/>Votre citation est maintenant visible sur Kotado. Vous pouvez dès à  présent la partager ou la commenter si vous le souhaitez !<br/><br/><br/>Si vous voulez voir votre citation, <a href=\"http://".$domain."/quote-$id_quote\" target=\"_blank\">cliquez ici</a>.<br/><br/><br/>Cordialement,<br/><b>The Kotado Team</b><br/><br/><br/><div style=\"border-top:1px dashed #CCCCCC\"></div><br/><br/>ENGLISH VERSION :<br/><br/>Hello <font color=\"#394DAC\"><b>$name_auteur</b></font> !<br/><br/>Your quote has been <font color=\"#394DAC\"><b>approved</b></font> recently by a member of our team ! <div style=\"background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px\">$texte_quote<br/><br/><a href=\"http://".$domain."/quote-$id_quote\" target=\"_blank\">#$id_quote</a><span style=\"float:right\">by <a href=\"http://".$domain."/user-$auteur_id\" target=\"_blank\">$name_auteur</a> on $date_quote</span></div>Congratulations !<br/><br/>Your quote is now visible on our website. You can share it or comment it if you want !<br/><br/><br/>If you want to see your quote, <a href=\"http://".$domain."/quote-$id_quote\" target=\"_blank\">click here</a>.<br/><br/><br/>Sincerely,<br/><b>The Kotado Team</b><br/><br/><br/>$end_mail";
				$mail = mail($email_auteur, "Citation approuvée", $message, $headers);
			}
		}

		$ids_quotes_posted_today .= ''.$id_quote;
		$ids_quotes_posted_today .= ",";
	}

	$ids_quotes_posted_today = substr($ids_quotes_posted_today, 0, strlen($ids_quotes_posted_today)-1);

	if ($affected_rows >= 1)
		MailPostedToday($ids_quotes_posted_today);
}
	
function email_birthday()
{
	include 'config.php';

	$data = domaine();
	$domain = $data[0];
	$name_website = $data[1];

	$date_today = date("d/m");
	$date_today .= '/%';
	$i = 0;
	$txt_file = 'Birthdays on '.$date_today."\r\n\n";

	$query = mysql_query("SELECT username, email, birth_date FROM teen_quotes_account WHERE birth_date LIKE '$date_today'");
	if (mysql_num_rows($query) >= 1)
	{
		while ($donnees = mysql_fetch_array($query))
		{
			$email_user = $donnees['email'];
			$username = ucfirst($donnees['username']);
			$age = age($donnees['birth_date']);

			if ($domain == $domain_en)
			{
				$email_subject = 'Happy birthday '.$username.'!';
				$email_message = $top_mail.'
				Hello <font color="#394DAC"><b>'.$username.'</b></font>,<br/>
				<img src="http://teen-quotes.com/mail/birthday.png" style="width:128px;height:128px;display:block;float:right;margin:0px 0px 0px 10px" />
				<br/>Wow, '.$age.' years old, that\'s great! All the team want to wish you a happy birthday! We hope that you will have a great day :)<br/>
				<br/>
				See you soon on '.$name_website.'!<br/>
				<br/>
				Best regards,<br/>
				The <b>'.$name_website.' Team</b>'.$end_mail;
			}
			elseif ($domain == $domain_fr)
			{
				$email_subject = 'Joyeux anniversaire '.$username.' !';
				$email_message = $top_mail.'
				Bonjour <font color="#394DAC"><b>'.$username.'</b></font>,<br/>
				<img src="http://teen-quotes.com/mail/birthday.png" style="width:128px;height:128px;display:block;float:right;margin:0px 0px 0px 10px" />
				<br/>Wow, '.$age.' ans, ça fait un paquet d\'années ! Toute l\'équipe vous souhaite un joyeux anniversaire ! Nous espérons que vous passerez une bonne journée :)<br/>
				<br/>
				À bientôt sur '.$name_website.' !<br/>
				<br/>
				Cordialement,<br/>
				The <b>'.$name_website.' Team</b>'.$end_mail;
				
			}
			
			$mail = mail($email_user, $email_subject, $email_message, $headers);

			if ($mail)
			{
				$i++;
				$txt_file .= '#'.$i.' : '.$username.' - '.$age."\r";
			}
		}

		$monfichier = fopen('../files/birthdays.txt', 'r+');
		fseek($monfichier, 0);
		fputs($monfichier, $txt_file);
		fclose($monfichier);
	}
}

function select_country($country)
{
	// Grant acces to variables
	global $common_choices, $other_countries;

	$country = ucfirst($country);
	$str = '
	<select name="country" style="width:197px;">
		<optgroup label="'.$common_choices.'">
			<option value="United States" selected="selected">United States</option> 
			<option value="Canada">Canada</option> 
			<option value="United Kingdom" >United Kingdom</option>
			<option value="France">France</option> 
			<option value="Ireland" >Ireland</option>
			<option value="Australia" >Australia</option>
			<option value="New Zealand" >New Zealand</option>
		</optgroup>
		<optgroup label="'.$other_countries.'">
			<option value="Afghanistan">Afghanistan</option> 
			<option value="Albania">Albania</option> 
			<option value="Algeria">Algeria</option> 
			<option value="American Samoa">American Samoa</option> 
			<option value="Andorra">Andorra</option> 
			<option value="Angola">Angola</option> 
			<option value="Anguilla">Anguilla</option> 
			<option value="Antarctica">Antarctica</option> 
			<option value="Antigua and Barbuda">Antigua and Barbuda</option> 
			<option value="Argentina">Argentina</option> 
			<option value="Armenia">Armenia</option> 
			<option value="Aruba">Aruba</option> 
			<option value="Australia">Australia</option> 
			<option value="Austria">Austria</option> 
			<option value="Azerbaijan">Azerbaijan</option> 
			<option value="Bahamas">Bahamas</option> 
			<option value="Bahrain">Bahrain</option> 
			<option value="Bangladesh">Bangladesh</option> 
			<option value="Barbados">Barbados</option> 
			<option value="Belarus">Belarus</option> 
			<option value="Belgium">Belgium</option> 
			<option value="Belize">Belize</option> 
			<option value="Benin">Benin</option> 
			<option value="Bermuda">Bermuda</option> 
			<option value="Bhutan">Bhutan</option> 
			<option value="Bolivia">Bolivia</option> 
			<option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option> 
			<option value="Botswana">Botswana</option> 
			<option value="Bouvet Island">Bouvet Island</option> 
			<option value="Brazil">Brazil</option> 
			<option value="British Indian Ocean Territory">British Indian Ocean Territory</option> 
			<option value="Brunei Darussalam">Brunei Darussalam</option> 
			<option value="Bulgaria">Bulgaria</option> 
			<option value="Burkina Faso">Burkina Faso</option> 
			<option value="Burundi">Burundi</option> 
			<option value="Cambodia">Cambodia</option> 
			<option value="Cameroon">Cameroon</option> 
			<option value="Canada">Canada</option> 
			<option value="Cape Verde">Cape Verde</option> 
			<option value="Cayman Islands">Cayman Islands</option> 
			<option value="Central African Republic">Central African Republic</option> 
			<option value="Chad">Chad</option> 
			<option value="Chile">Chile</option> 
			<option value="China">China</option> 
			<option value="Christmas Island">Christmas Island</option> 
			<option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option> 
			<option value="Colombia">Colombia</option> 
			<option value="Comoros">Comoros</option> 
			<option value="Congo">Congo</option> 
			<option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option> 
			<option value="Cook Islands">Cook Islands</option> 
			<option value="Costa Rica">Costa Rica</option> 
			<option value="Cote D\'ivoire">Cote D\'ivoire</option> 
			<option value="Croatia">Croatia</option> 
			<option value="Cuba">Cuba</option> 
			<option value="Cyprus">Cyprus</option> 
			<option value="Czech Republic">Czech Republic</option> 
			<option value="Denmark">Denmark</option> 
			<option value="Djibouti">Djibouti</option> 
			<option value="Dominica">Dominica</option> 
			<option value="Dominican Republic">Dominican Republic</option> 
			<option value="Ecuador">Ecuador</option> 
			<option value="Egypt">Egypt</option> 
			<option value="El Salvador">El Salvador</option> 
			<option value="Equatorial Guinea">Equatorial Guinea</option> 
			<option value="Eritrea">Eritrea</option> 
			<option value="Estonia">Estonia</option> 
			<option value="Ethiopia">Ethiopia</option> 
			<option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option> 
			<option value="Faroe Islands">Faroe Islands</option> 
			<option value="Fiji">Fiji</option> 
			<option value="Finland">Finland</option> 
			<option value="French Guiana">French Guiana</option> 
			<option value="French Polynesia">French Polynesia</option> 
			<option value="French Southern Territories">French Southern Territories</option> 
			<option value="Gabon">Gabon</option> 
			<option value="Gambia">Gambia</option> 
			<option value="Georgia">Georgia</option> 
			<option value="Germany">Germany</option> 
			<option value="Ghana">Ghana</option> 
			<option value="Gibraltar">Gibraltar</option> 
			<option value="Greece">Greece</option> 
			<option value="Greenland">Greenland</option> 
			<option value="Grenada">Grenada</option> 
			<option value="Guadeloupe">Guadeloupe</option> 
			<option value="Guam">Guam</option> 
			<option value="Guatemala">Guatemala</option> 
			<option value="Guinea">Guinea</option> 
			<option value="Guinea-bissau">Guinea-bissau</option> 
			<option value="Guyana">Guyana</option> 
			<option value="Haiti">Haiti</option> 
			<option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option> 
			<option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option> 
			<option value="Honduras">Honduras</option> 
			<option value="Hong Kong">Hong Kong</option> 
			<option value="Hungary">Hungary</option> 
			<option value="Iceland">Iceland</option> 
			<option value="India">India</option> 
			<option value="Indonesia">Indonesia</option> 
			<option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option> 
			<option value="Iraq">Iraq</option> 
			<option value="Ireland">Ireland</option> 
			<option value="Israel">Israel</option> 
			<option value="Italy">Italy</option> 
			<option value="Jamaica">Jamaica</option> 
			<option value="Japan">Japan</option> 
			<option value="Jordan">Jordan</option> 
			<option value="Kazakhstan">Kazakhstan</option> 
			<option value="Kenya">Kenya</option> 
			<option value="Kiribati">Kiribati</option> 
			<option value="Korea, Democratic People\'s Republic of">Korea, Democratic People\'s Republic of</option> 
			<option value="Korea, Republic of">Korea, Republic of</option> 
			<option value="Kuwait">Kuwait</option> 
			<option value="Kyrgyzstan">Kyrgyzstan</option> 
			<option value="Lao People\'s Democratic Republic">Lao People\'s Democratic Republic</option> 
			<option value="Latvia">Latvia</option> 
			<option value="Lebanon">Lebanon</option> 
			<option value="Lesotho">Lesotho</option> 
			<option value="Liberia">Liberia</option> 
			<option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option> 
			<option value="Liechtenstein">Liechtenstein</option> 
			<option value="Lithuania">Lithuania</option> 
			<option value="Luxembourg">Luxembourg</option> 
			<option value="Macao">Macao</option> 
			<option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option> 
			<option value="Madagascar">Madagascar</option> 
			<option value="Malawi">Malawi</option> 
			<option value="Malaysia">Malaysia</option> 
			<option value="Maldives">Maldives</option> 
			<option value="Mali">Mali</option> 
			<option value="Malta">Malta</option> 
			<option value="Marshall Islands">Marshall Islands</option> 
			<option value="Martinique">Martinique</option> 
			<option value="Mauritania">Mauritania</option> 
			<option value="Mauritius">Mauritius</option> 
			<option value="Mayotte">Mayotte</option> 
			<option value="Mexico">Mexico</option> 
			<option value="Micronesia, Federated States of">Micronesia, Federated States of</option> 
			<option value="Moldova, Republic of">Moldova, Republic of</option> 
			<option value="Monaco">Monaco</option> 
			<option value="Mongolia">Mongolia</option> 
			<option value="Montserrat">Montserrat</option> 
			<option value="Morocco">Morocco</option> 
			<option value="Mozambique">Mozambique</option> 
			<option value="Myanmar">Myanmar</option> 
			<option value="Namibia">Namibia</option> 
			<option value="Nauru">Nauru</option> 
			<option value="Nepal">Nepal</option> 
			<option value="Netherlands">Netherlands</option> 
			<option value="Netherlands Antilles">Netherlands Antilles</option> 
			<option value="New Caledonia">New Caledonia</option> 
			<option value="New Zealand">New Zealand</option> 
			<option value="Nicaragua">Nicaragua</option> 
			<option value="Niger">Niger</option> 
			<option value="Nigeria">Nigeria</option> 
			<option value="Niue">Niue</option> 
			<option value="Norfolk Island">Norfolk Island</option> 
			<option value="Northern Mariana Islands">Northern Mariana Islands</option> 
			<option value="Norway">Norway</option> 
			<option value="Oman">Oman</option> 
			<option value="Pakistan">Pakistan</option> 
			<option value="Palau">Palau</option> 
			<option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option> 
			<option value="Panama">Panama</option> 
			<option value="Papua New Guinea">Papua New Guinea</option> 
			<option value="Paraguay">Paraguay</option> 
			<option value="Peru">Peru</option> 
			<option value="Philippines">Philippines</option> 
			<option value="Pitcairn">Pitcairn</option> 
			<option value="Poland">Poland</option> 
			<option value="Portugal">Portugal</option> 
			<option value="Puerto Rico">Puerto Rico</option> 
			<option value="Qatar">Qatar</option> 
			<option value="Reunion">Reunion</option> 
			<option value="Romania">Romania</option> 
			<option value="Russian Federation">Russian Federation</option> 
			<option value="Rwanda">Rwanda</option> 
			<option value="Saint Helena">Saint Helena</option> 
			<option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option> 
			<option value="Saint Lucia">Saint Lucia</option> 
			<option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option> 
			<option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option> 
			<option value="Samoa">Samoa</option> 
			<option value="San Marino">San Marino</option> 
			<option value="Sao Tome and Principe">Sao Tome and Principe</option> 
			<option value="Saudi Arabia">Saudi Arabia</option> 
			<option value="Senegal">Senegal</option> 
			<option value="Serbia and Montenegro">Serbia and Montenegro</option> 
			<option value="Seychelles">Seychelles</option> 
			<option value="Sierra Leone">Sierra Leone</option> 
			<option value="Singapore">Singapore</option> 
			<option value="Slovakia">Slovakia</option> 
			<option value="Slovenia">Slovenia</option> 
			<option value="Solomon Islands">Solomon Islands</option> 
			<option value="Somalia">Somalia</option> 
			<option value="South Africa">South Africa</option> 
			<option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option> 
			<option value="Spain">Spain</option> 
			<option value="Sri Lanka">Sri Lanka</option> 
			<option value="Sudan">Sudan</option> 
			<option value="Suriname">Suriname</option> 
			<option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option> 
			<option value="Swaziland">Swaziland</option> 
			<option value="Sweden">Sweden</option> 
			<option value="Switzerland">Switzerland</option> 
			<option value="Syrian Arab Republic">Syrian Arab Republic</option> 
			<option value="Taiwan, Province of China">Taiwan, Province of China</option> 
			<option value="Tajikistan">Tajikistan</option> 
			<option value="Tanzania, United Republic of">Tanzania, United Republic of</option> 
			<option value="Thailand">Thailand</option> 
			<option value="Timor-leste">Timor-leste</option> 
			<option value="Togo">Togo</option> 
			<option value="Tokelau">Tokelau</option> 
			<option value="Tonga">Tonga</option> 
			<option value="Trinidad and Tobago">Trinidad and Tobago</option> 
			<option value="Tunisia">Tunisia</option> 
			<option value="Turkey">Turkey</option> 
			<option value="Turkmenistan">Turkmenistan</option> 
			<option value="Turks and Caicos Islands">Turks and Caicos Islands</option> 
			<option value="Tuvalu">Tuvalu</option> 
			<option value="Uganda">Uganda</option> 
			<option value="Ukraine">Ukraine</option> 
			<option value="United Arab Emirates">United Arab Emirates</option> 
			<option value="United Kingdom">United Kingdom</option> 
			<option value="United States">United States</option> 
			<option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option> 
			<option value="Uruguay">Uruguay</option> 
			<option value="Uzbekistan">Uzbekistan</option> 
			<option value="Vanuatu">Vanuatu</option> 
			<option value="Venezuela">Venezuela</option> 
			<option value="Viet Nam">Viet Nam</option> 
			<option value="Virgin Islands, British">Virgin Islands, British</option> 
			<option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option> 
			<option value="Wallis and Futuna">Wallis and Futuna</option> 
			<option value="Western Sahara">Western Sahara</option> 
			<option value="Yemen">Yemen</option> 
			<option value="Zambia">Zambia</option> 
			<option value="Zimbabwe">Zimbabwe</option>
		</optgroup>
	</select>';
		if (strstr($str, 'value="'.$country.'"')) 
		{
			$str = str_replace('selected="selected"', '', $str);
			$str = str_replace('value="'.$country.'"', 'value="'.$country.'" selected="selected"', $str);
		}
	echo $str;
}


function MailRandomQuote ($nombre) 
{
	// Grant access to these variables
	global $domain_en, $domain_fr;

	$data = domaine();
	$domain = $data[0];
	$name_website = $data[1];

	$query = mysql_query('SELECT q.id, q.texte_english texte_english,q.date date, a.username auteur, q.auteur_id auteur_id FROM teen_quotes_quotes q, teen_quotes_account a WHERE q.approved = 1 AND q.auteur_id = a.id ORDER BY RAND() LIMIT '.$nombre.'');

	while($donnees = mysql_fetch_array($query)) 
	{
		$txt_quote = $donnees['texte_english'];
		$id_quote  = $donnees['id'];
		$auteur    = $donnees['auteur'];
		$auteur_id = $donnees['auteur_id'];
		$date      = $donnees['date'];

		$email_txt.= '<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">';

		if ($domain == $domain_en)
			$email_txt.= ''.$txt_quote.'<br/><div style="font-size:90%;margin-top:5px"><a href="http://'.$domain.'/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://'.$domain.'/user-'.$auteur_id.'" target="_blank">'.$auteur.'</a> on '.$date.'</span></div>';	
		elseif ($domain == $domain_fr)
			$email_txt.= ''.$txt_quote.'<br/><div style="font-size:90%;margin-top:5px"><a href="http://'.$domain.'/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">par <a href="http://'.$domain.'/user-'.$auteur_id.'" target="_blank">'.$auteur.'</a> le '.$date.'</span></div>';

		$email_txt.= '</div>';
	}

	return $email_txt;
}
	
function MailPostedToday ($id_quote) 
{
	include "config.php";

	$data = domaine();
	$domain = $data[0];
	$name_website = $data[1];

	if (!empty($id_quote))
	{
		$tooltip = getRandomTooltip();

		$id_quote = str_replace(',', '\',\'', $id_quote);
		$query = mysql_query("SELECT q.id id, q.texte_english texte_english, q.date date, a.username auteur, q.auteur_id auteur_id FROM teen_quotes_quotes q, teen_quotes_account a WHERE q.approved = '1' AND q.id IN ('".$id_quote."') AND q.auteur_id = a.id ORDER BY q.id DESC");

		while ($donnees = mysql_fetch_array($query)) 
		{
			$txt_quote = $donnees['texte_english'];
			$id_quote  = $donnees['id'];
			$auteur    = $donnees['auteur'];
			$auteur_id = $donnees['auteur_id'];
			$date      = $donnees['date'];

			$email_txt.= '<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:20px 5px">';

			if ($domain == $domain_en)
				$email_txt.= ''.$txt_quote.'<br/><div style="font-size:90%;margin-top:5px"><a href="http://'.$domain.'/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://'.$domain.'/user-'.$auteur_id.'" target="_blank">'.$auteur.'</a> on '.$date.'</span></div>';	
			elseif ($domain == $domain_fr)
				$email_txt.= ''.$txt_quote.'<br/><div style="font-size:90%;margin-top:5px"><a href="http://'.$domain.'/quote-'.$id_quote.'" target="_blank">#'.$id_quote.'</a><span style="float:right">par <a href="http://'.$domain.'/user-'.$auteur_id.'" target="_blank">'.$auteur.'</a> le '.$date.'</span></div>';

			$email_txt.= '</div>';
		}

		// Add the tooltip at the end of the email
		$email_txt .= '<br/>'.$tooltip.'<br/>';

		$today = date("d/m/Y");
		$nb_email_send = 0;
		$search_email = mysql_query("SELECT email, code_unsubscribe FROM newsletters WHERE type = 'daily'");

		while ($donnees = mysql_fetch_array($search_email))
		{
			$email = $donnees['email'];
			$code = $donnees['code_unsubscribe'];

			if ($domain == $domain_en)
			{
				$email_subject = 'Quotes of the day';
				$message = ''.$top_mail.'Here are the quotes posted today ('.$today.'):<br/><br/>'.$email_txt.$end_mail;
				$message .= '<br/><span style="font-size:80%">This email was adressed to you ('.$email.') because you are subscribed to our newsletter. If you want to unsubscribe, please follow <a href="http://'.$domain.'/newsletter.php?action=unsubscribe_everyday&email='.$email.'&code='.$code.'" target="_blank"> this link</a>.</span>';
			}
			elseif ($domain == $domain_fr)
			{
				$email_subject = 'Citations du jour';
				$message = ''.$top_mail.'Voici les citations publiées aujourd\'hui ('.$today.') :<br/><br/>'.$email_txt.$end_mail;
				$message .= '<br/><span style="font-size:80%">Cet email a été envoyé à votre adresse ('.$email.') car vous êtes inscrit à la newsletter. Si vous souhaitez vous désinscrire, cliquez sur <a href="http://'.$domain.'/newsletter.php?action=unsubscribe_everyday&email='.$email.'&code='.$code.'" target="_blank"> ce lien</a>.</span>.';
			}

			$mail = mail($email, $email_subject.' - '.$today, $message, $headers);
		}

		$monfichier = fopen('../files/compteur_email_quotidien.txt', 'r+'); // Ouverture du fichier
		fseek($monfichier, 0); // On remet le curseur au début du fichier
		fputs($monfichier, ''.$today.' : '.$nb_email_send.''); // On écrit le nouveau nombre de pages vues
		fclose($monfichier);
	}
}

function cut_tweet($chaine)
{
	// Grant access to these variables
	global $domain_en, $domain_fr;

	$data = domaine();
	$domain = $data[0];
	$name_website = $data[1];

	$lg_max = 117;

	if ($domain == $domain_en)
	{
		$longueur_max_ajout_twitter = 105;
		$username_twitter = '@ohteenquotes';
	}
	elseif ($domain == $domain_fr)
	{
		$longueur_max_ajout_twitter = 108;
		$username_twitter = '@Kotado_';	
	}
	
	if (strlen($chaine) > $lg_max) 
	{
		$chaine = substr($chaine, 0, $lg_max);
		$last_space = strrpos($chaine, " "); 

		// After the space, add ...   
		$chaine = substr($chaine, 0, $last_space);
		$chaine .= '...';
	}
	elseif (strlen($chaine) <= $longueur_max_ajout_twitter)
	{
		$chaine .= ' '.$username_twitter;
	}

	$search = array ('%', ' ', '"');
	$replace = array('%25', '%20', '%34');
	$chaine = str_replace($search, $replace, $chaine);
	return $chaine;
}

function cut_comment($chaine)
{
	$lg_max = 100;

	if (strlen($chaine) > $lg_max) 
	{
		$chaine1 = substr($chaine, 0, $lg_max);
		$last_space = strrpos($chaine1, " "); 

		// After the space, add ...   
		$chaine1 = substr($chaine1, 0, $last_space);
		$chaine1 .= '...';

		return $chaine1;
	}
	else
		return $chaine;
}

function afficher_nb_comments($nombre_commentaires)
{
	// Grant access to variables for lang
	global $comments, $comment, $no_comments;

	// Desktop
	if (!isUrlMobile())
	{
		if ($nombre_commentaires >= 1)
			echo '<span class="box_nb_comments">'.$nombre_commentaires.'</span>';
		else
			echo '<span class="no_comments">'.$no_comments.'</span>';	
	}
	// Mobile
	else
	{
		if ($nombre_commentaires >= 1)
			echo '<span class="box_nb_comments">'.$nombre_commentaires.'</span>';
	}
}

function afficher_favori($id_quote, $is_favorite, $logged, $id_user=0) 
{
	// Grant access to variables for language
	global $add_favorite, $unfavorite;

	// Desktop
	if (!isUrlMobile())
	{
		if ($logged AND $is_favorite == '0') 
			echo '<span class="favorite fade_jquery" data-id ="'.$id_quote.'"><a href="" onclick="favorite('.$id_quote.','.$id_user.');return false;" title="'.$add_favorite.'"><span class="heart_fav on"></span></a></span>';
		elseif ($logged AND $is_favorite == '1')
			echo '<span class="favorite fade_jquery" data-id ="'.$id_quote.'"><a href="" onclick="unfavorite('.$id_quote.','.$id_user.'); return false;" title="'.$unfavorite.'"><span class="heart_fav off"></span></a></span>';
	}
	// Mobile
	else
	{
		if ($logged AND $is_favorite == '0')
			echo '<span class="favorite"><a href="favorite-'.$id_quote.'" title="'.$add_favorite.'"><span class="heart_fav on"></span></a></span>';
		elseif ($logged AND $is_favorite == '1')
			echo '<span class="favorite"><a href="unfavorite-'.$id_quote.'" title="'.$unfavorite.'"><span class="heart_fav off"></span></a></span>';
	}

}

function share_fb_twitter ($id_quote, $txt_quote) 
{
	// Grant access to variable for lang
	global $share;

	$data = domaine();
	$domain = $data[0];
	$name_website = $data[1];

	$txt_tweet = cut_tweet($txt_quote);
	$url_encode = urlencode('http://'.$domain.'/quote-'.$id_quote.'');

	echo '<div class="share_fb_twitter"><span class="fade_jquery"><iframe src="//www.facebook.com/plugins/like.php?href= '.$url_encode.'&amp;send=false&amp;layout=button_count&amp;width=110&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:110px; height:21px;" allowTransparency="true"></iframe></span><span class="right fade_jquery"><a href="http://twitter.com/share?url=http://'.$domain.'/quote-'.$id_quote.'&text='.$txt_tweet.'" class="twitter-share-button" data-count="none">Tweet</a></span></div>';
}

function date_et_auteur ($auteur_id, $auteur, $date_quote) 
{
	// Grant access to variables for lang
	global $on, $by, $view_his_profile;

	// Desktop
	if (!isUrlMobile())
		echo '<span class="right">'.$by.' <a href="user-'.$auteur_id.'" title="'.$view_his_profile.'">'.$auteur.'</a> '.$on.' '.$date_quote.'</span><br/>';
	// Mobile
	else
		// Spaces are IN the link for touchscreen (easier to click)
		echo '<span class="right">'.$by.'<a href="user-'.$auteur_id.'" title="'.$view_his_profile.'"> '.$auteur.' </a>'.$on.' '.$date_quote.'</span><br/>';
}

function is_quote_exist ($txt_quote) 
{
	$txt_quote_cut = cut_tweet($txt_quote);
	$quote_exist = mysql_num_rows(mysql_query("SELECT id FROM teen_quotes_quotes WHERE texte_english LIKE '%$txt_quote_cut%' AND approved = '1'"));

	return ($quote_exist >= 1);
}

function nl2br_to_textarea ($texte) 
{
	$line_break = PHP_EOL;
	$patterns = array("/(<br>|<br \/>|<br\/>)\s*/i","/(\r\n|\r|\n)/");
	$replacements = array(PHP_EOL, $line_break);
	$string = preg_replace($patterns, $replacements, $texte);

	return $string;
}

// Set a cookie to force the desktop view
if (isset($_GET['mobile'])) 
{
	$data = domaine();
	$domain = $data[0];
	$name_website = $data[1];

	setcookie("mobile", 1 , time() + (((3600*24)*30)*12), null, '.'.$domain.'', false, true);
}

function getSubDomain()
{
	return strstr($_SERVER['HTTP_HOST'], '.', true);
}

function subDomainIsRestricted($subDomain)
{
	$restricted_sub_domains = array(
		"stories", "statistics");

	return (in_array($subDomain, $restricted_sub_domains));
}

function mobile_device_detect ()
{
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	
	// Get the main domain
	$data = domaine();
	$domain = $data[0];
	$name_website = $data[1];

	// Do not redirect when we hit a restricted subdomain
	if (subDomainIsRestricted(getSubDomain()))
		$redirect_mobile = 'http://m.'.$domain.'/'.getSubDomain();  
	else
		$redirect_mobile = 'http://m.'.$domain.$_SERVER['REQUEST_URI'];

	// Grant access to the link for the iOS app and Android app
	global $link_app_iphone, $link_app_android;

	// Force enter in the switch
	switch (true) 
	{
		case (mb_eregi('ipod', $user_agent)||mb_eregi('iphone', $user_agent)); // we find the words iphone or ipod in the user agent
			if (preg_match('#apps#', $_SERVER['REQUEST_URI']) AND $link_app_iphone != '#')
				$redirect_mobile = $link_app_iphone;
		break; // break out and skip the rest if we've had a match on the iphone or ipod

		case (mb_eregi('android', $user_agent));  // we find android in the user agent
			if (preg_match('#apps#', $_SERVER['REQUEST_URI']) AND $link_app_android != '#')
				$redirect_mobile = $link_app_android;
		break; // break out and skip the rest if we've had a match on android

		default;
			$redirect_mobile = $redirect_mobile;
	}

	// If we detect a mobile, do the redirection
	if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $user_agent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($user_agent,0,4)))
	{
		header('Location: '.$redirect_mobile.'');
		exit();
	}

} // ends function mobile_device_detect

if (empty($_COOKIE['mobile']) AND !isUrlMobile() AND !isset($_GET['mobile']))
{
	mobile_device_detect();
}

function isUrlMobile()
{
	return (preg_match('#http://m\.#', $_SERVER['SCRIPT_URI']));
}

// Alias of isUrlMobile()
function isMobile()
{
	return isUrlMobile();
}

// Opposite of isUrlMobile()
function isDesktop()
{
	return !isUrlMobile();
}

function isDomainValidForAjax()
{
	global $domain_fr, $domain_en;
	
	return (preg_match('/'.$domain_fr.'/', $_SERVER['SERVER_NAME']) OR preg_match('/'.$domain_en.'/', $_SERVER['SERVER_NAME']));
}

function arrayToVar($array)
{
	global $domain, $name_website, $language;

	return ${$array[1]};
}

function generateTrad ($lang, $languageFile)
{
	global $name_website;
	require $languageFile;
	$string = "";

	if (is_array($lang))
	{
		foreach ($lang as $lg) 
		{
			$trad = ${$lg};

			if (!empty($trad))
				$string .= "var ".$lg." = '".$trad."';";
			else
			{
				echo 'Translate error! '.$lg;
				die();
			}
		}
		return $string;
	}

	return false;
}
function getRandomTooltip()
{
	global $language;

	$query = mysql_query("SELECT * FROM tooltips ORDER BY RAND() LIMIT 0,1");
	$data = mysql_fetch_array($query);

	$content = preg_replace_callback("#%([a-zA-Z_]+)%#isU", 'arrayToVar', $data[$language]);

	return $content;
}

function hint ($position, $txt, $type=false, $return=false)
{
	$class = $position;

	if ($type != false)
		$class .= ' hint--'.$type;

	if (!$return)
		echo 'class="hint--'.$class.'" data-hint="'.$txt.'"';
	else
		return 'class="hint--'.$class.'" data-hint="'.$txt.'"';
}

function insertConnexion($type, $id_user=null)
{
	if ($id_user === null)
		$id_user = $_SESSION['id'];

	if (!empty($id_user))
		mysql_query("INSERT INTO connexions_log (id_user, type) VALUES ('".$id_user."', '".$type."')");
}

function addMember ($username, $email, $passwordOne, $passwordConfirm)
{
	global $language;
	include 'lang/'.$language.'/signup.php';

	$errors = array();
	$code = caracteresAleatoires(5);
	

	/*
	 * USERNAME
	 */

	$numberOfMember = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_account WHERE username = '$username'"));
	if (strlen(trim($username)) < 5)//Username is too short
		array_push($errors, $username_short);
	else if (!usernameIsValid($username))//Username is not valid
		array_push($errors, $username_not_valid);
	else if ($numberOfMember != 0)//Username is not valid
		array_push($errors, $username_taken);

	/*
	 * PASSWORD
	 */

	if (strlen($passwordOne) < 6)//If password is too short
		array_push($errors, $password_short);
	else if ($passwordOne != $passwordConfirm && $passwordConfirm != "")//If passwords doesn't match
		array_push($errors, $password_not_same);
		
	/*
	 * EMAIL
	 */

	$numberOfEmail = mysql_num_rows(mysql_query("SELECT * FROM teen_quotes_account WHERE email = '$email'"));
	if (strlen($email) < 6 || !preg_match("#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email))
		array_push($errors, $email_incorrect);
	else if ($numberOfEmail != 0)
		array_push($errors, $email_taken);

	/*
	 * ADD MEMBER TO DATABASE
	 */

	if (count($errors) == 0)
	{
		$ip = $_SERVER["REMOTE_ADDR"];//IP
		$pass = sha1(strtoupper($username).':'.strtoupper($passwordOne));//PASS

		$add = mysql_query("INSERT INTO teen_quotes_account (username, pass, email, ip, security_level, location_signup) VALUES ('$username', '$pass', '$email', '$ip', '0', 'website')");
		
		if (!$add)
			array_push($errors, $error);
	}

	return $errors;
}
?>
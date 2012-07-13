<?php				$query = mysql_query("SELECT * FROM approve_quotes WHERE send = '0' AND id_user = '".$auteur_id."'");

				$approved_quote_txt = '';
				$unapproved_quote_txt = '';
				$nb_quote_approved = 0;
				$nb_quote_unapproved = 0;

				while ($data = mysql_fetch_array($query))
				{
					$id_quote = $data['id_quote'];
					$id_user = $auteur_id;
					$approved = $data['approved'];
					list($date, $jours_posted) = explode('-', $data['quote_release']);
					$edit = $data['edit'];

					if ($jours_posted > '1')
					{
						$days_quote_posted = $days_quote_posted.'s';
					}

					if ($edit == '1')
					{
						if (preg_match('/'.$domaine_fr.'/', $_SERVER['SERVER_NAME']))
						{
							$edit_message = '<br><br /><b>Votre citation a été modifiée par notre équipe avant son approbation. Veuillez respecter la syntaxe, l\'orthographe et le sens de votre citation.</b>';
						}
						if (preg_match('/'.$domaine_en.'/', $_SERVER['SERVER_NAME']))
						{
							$edit_message = '<br><br /><b>Your quote has been modified by our team before approval. Please follow the syntax, the spelling and the meaning of your quote.</b>';
						}
					}
					else
					{
						$edit_message = '';
					}

					$query_texte_quote = mysql_fetch_array(mysql_query("SELECT q.texte_english texte_english, q.date date, a.email email, a.username username FROM teen_quotes_quotes q, teen_quotes_account a WHERE q.auteur_id = a.id AND q.id = '".$id_quote."'"));
					$texte_quote = $query_texte_quote['texte_english'];
					$date_quote = $query_texte_quote['date'];
					$email_auteur = $query_texte_quote['email'];
					$name_auteur = $query_texte_quote['username'];

					if ($approved == '1')
					{
						if (preg_match('/'.$domaine_fr.'/', $_SERVER['SERVER_NAME']))
						{	
							$approved_quote_txt .= '<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br><br /><a href="http://'.$domaine.'" target="_blank">#'.$id_quote.'</a><span style="float:right">par <a href="http://'.$domaine.'user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> le '.$date_quote.'</span></div>Elle sera publiée le '.$date.' ('.$jours_posted.' '.$days_quote_posted.'), vous recevrez un email quand elle sera publiée sur le site.'.$edit_message.'<br><br />';
						}
						elseif (preg_match('/'.$domaine_en.'/', $_SERVER['SERVER_NAME']))
						{
							$approved_quote_txt .= '<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br><br /><a href="http://'.$domaine.'" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://'.$domaine.'user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> on '.$date_quote.'</span></div>It will be released on '.$date.' ('.$jours_posted.' '.$days_quote_posted .'), you will receive an email when it will be posted on the website.'.$edit_message.'<br><br />';
						}

						$nb_quote_approved++;
					}
					elseif ($approved == '0')
					{
						if (preg_match('/'.$domaine_fr.'/', $_SERVER['SERVER_NAME']))
						{
							$unapproved_quote_txt .= '<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br><br /><a href="http://'.$domaine.'" target="_blank">#'.$id_quote.'</a><span style="float:right">par <a href="http://'.$domaine.'user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> le '.$date_quote.'</span></div><br><br />';
						}
						elseif (preg_match('/'.$domaine_en.'/', $_SERVER['SERVER_NAME']))
						{
							$unapproved_quote_txt .= '<div style="background:#f5f5f5;border:1px solid #e5e5e5;padding:10px;margin:30px 10px">'.$texte_quote.'<br><br /><a href="http://'.$domaine.'" target="_blank">#'.$id_quote.'</a><span style="float:right">by <a href="http://'.$domaine.'user-'.$auteur_id.'" target="_blank">'.$name_auteur.'</a> on '.$date_quote.'</span></div><br><br />';
						}

						$nb_quote_unapproved++;
					}		
				} // FIN DU WHILE

				$update_send = mysql_query("UPDATE approve_quotes SET send = '1' WHERE id_user = '".$auteur_id."'");

				if (preg_match('/'.$domaine_fr.'/', $_SERVER['SERVER_NAME']))
				{
					
					$email_subject = 'Modération de vos citations';

					$final_mail = ''.$top_mail.'Bonjour <font color="#5C9FC0"><b>'.$name_auteur.'</b></font> !<br><br />';

					if ($nb_quote_approved >= 1)
					{
						if ($nb_quote_approved == 1)
						{
							$final_mail .= '<b>La citation suivante a été approuvée :</b>'.$approved_quote_txt;
						}
						else
						{	
							$final_mail .= '<b>Les citations suivantes ('.$nb_quote_approved.') ont été approuvées :</b>'.$approved_quote_txt;
						}
					}

					if ($nb_quote_unapproved >= 1)
					{
						if ($nb_quote_unapproved == 1)
						{
							$final_mail .= '<b>La citation suivante a été rejetée :</b>'.$unapproved_quote_txt;
						}
						else
						{
							$final_mail .= '<b>Les citations suivantes ('.$nb_quote_unapproved.') ont été rejetées :</b>'.$unapproved_quote_txt;
						}
					}

					$final_mail .= '<br>Cordialement,<br><b>The '.$name_website.' Team</b>'.$end_mail;
				}
				elseif (preg_match('/'.$domaine_en.'/', $_SERVER['SERVER_NAME']))
				{
					$email_subject = 'Moderation of your quotes';

					$final_mail = ''.$top_mail.'Hello <font color="#5C9FC0"><b>'.$name_auteur.'</b></font> !<br><br />';

					if ($nb_quote_approved >= 1)
					{
						if ($nb_quote_approved == 1)
						{
							$final_mail .= '<b>This quote has been approved :</b>'.$approved_quote_txt;
						}
						else
						{	
							$final_mail .= '<b>The following quotes ('.$nb_quote_approved.') have been approved :</b>'.$approved_quote_txt;
						}
					}

					if ($nb_quote_unapproved >= 1)
					{
						if ($nb_quote_unapproved == 1)
						{
							$final_mail .= '<b>This quote has been rejected :</b>'.$unapproved_quote_txt;
						}
						else
						{
							$final_mail .= '<b>The following quotes ('.$nb_quote_unapproved.') have been rejected :</b>'.$unapproved_quote_txt;
						}
					}

					$final_mail .= '<br>Sincerely,<br><b>The '.$name_website.' Team</b>'.$end_mail;
				}

				$mail = mail($email_auteur, $email_subject, $final_mail, $headers);
				echo ''.$succes.' The author has been notified successfully';
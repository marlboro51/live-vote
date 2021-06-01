<?php
include('common.php');
$logged = getSession('login',0);
$action = getPost('action','');
$reunion = getSession('reunion',0);
if ($logged > 0 && $reunion > 0)
{
	switch ($action) {
	case 'sendcomment':
		$msg=getPost('comment','');
		if ($msg != '')
		{
			addComment($msg);
		}
		break;
	case 'deletecomment':
		$msg=getPost('comment','');
		if ($msg != '')
                {
                        deleteComment($msg);
                }
		break;
        case 'deleteresultat':
                $msg=getPost('resultat','');
                if ($msg != '')
                {
                        deleteResultat($msg);
                }
                break;
	case 'vote':
		$bulletin = getPost('bulletin','');
		$voteid = getPost('vote',0);
		if ($voteid != 0 && $bulletin != '')
		{
			$query = "SELECT COUNT(*) FROM PROCURATION WHERE PROCURATION_ListeId='$reunion' AND PROCURATION_GESRCId='$logged'";
			$aDonneProcu = SQL($query,"RC");

			$query = "SELECT COUNT(*) FROM PROCURATION WHERE PROCURATION_ListeId='$reunion' AND PROCURATION_GEDSTId='$logged'";
			$procusRecues = SQL($query,"RC");

			$query = "SELECT COUNT(*) FROM EMARGEMENT WHERE EMARGEMENT_GEId='$logged' AND EMARGEMENT_VoteId='$voteid'";
			$voted = SQL($query,"RC");
			if ($voted == 0 && $aDonneProcu == 0)
			{
				$select = explode("\r\n",$bulletin);
				$b = "|";
				foreach($select as $s)
				{
					$n = explode('choix',$s);
					if (isset($n[1])) $b .= $n[1] . '|';
				}
				$query = "INSERT INTO BULLETIN SET BULLETIN_VoteId='$voteid', BULLETIN_Choix='$b'";
				for ($i=0; $i<=$procusRecues; $i++)
				{
					SQL($query);
				}
				$query = "INSERT INTO EMARGEMENT SET EMARGEMENT_GEId='$logged', EMARGEMENT_VoteId='$voteid', EMARGEMENT_Procuration='$procusRecues'";
				SQL($query);
				printf("<div class='ok'>Vote pris en compte</div>\n");
			}
			else
				printf("<div class='error'>Vous avez d&eacute;j&agrave; vot&eacute;!</div>\n");
		}
		break;
	case 'getVote' :
		$query = "SELECT VOTE_Id, SUM(CASE WHEN BULLETIN_Id IS NULL THEN 0 ELSE 1 END) FROM VOTE LEFT OUTER JOIN BULLETIN ON VOTE_Id =BULLETIN_VoteId WHERE VOTE_ListeId='$reunion' AND VOTE_Status=1 GROUP BY VOTE_Id";
		$resultats = SQL($query);
		foreach ($resultats as $resultat)
		{
			printf("<span id='result_%s'>%s</span>",$resultat[0],$resultat[1]);
		}
		break;
	default:
		$datelimite = getSession('datelimite','2021-02-01 12:00:00');
		$datefin1 = $datelimite;
		$datefin2 = $datelimite;
		$datefin3 = $datelimite;
		$query = "SELECT MESSAGE_Txt, MESSAGE_Auteur, MESSAGE_Date, MESSAGE_Id from MESSAGE where MESSAGE_ReunionId='".$reunion."' and MESSAGE_Status = 1 and MESSAGE_Date > '".$datelimite."'";
		$res = SQL($query,"");

		foreach ($res as $msg) {
			print("<div id='comment".$msg[3]."'><h3>".$msg[1]."</h3><p>".$msg[0]."</p>");
			if (isOwner()) 
			{
				print("<input class='remove' type='button' onclick='deleteComment(".$msg[3].");' value='X'>\n");
			}
			printf("</div>\n");
			$datefin1 = $msg[2];
		}

		$query = "SELECT MESSAGE_Id, MESSAGE_Date FROM MESSAGE where MESSAGE_ReunionId='".$reunion."' and MESSAGE_Status = 0 and MESSAGE_Date > '".$datelimite."'";
		$res = SQL($query,"");
		foreach ($res as $msg) {
			printf("<script>$('#comment".$msg[0]."').remove();//".$datelimite."\n</script>");
			$datefin2 = $msg[1];
		}

		$query = "SELECT COUNT(*) FROM VOTE WHERE VOTE_ListeId=$reunion AND VOTE_Status=1 AND VOTE_Date > '".$datelimite."'";
		$nbVote = SQL($query,"RC");
		if ($nbVote > 0)
		{
			$query = "SELECT VOTE_Id, VOTE_Question, VOTE_Reponses, VOTE_Type, VOTE_Date FROM VOTE WHERE VOTE_ListeId=$reunion AND VOTE_Status=1 AND VOTE_Date > '".$datelimite."'";
			$res = SQL($query,"");

			foreach ($res as $vote)
			{

				$datefin3 = $vote[4];

				printf("<div id=vote>\n");
				printf("<h1>%s</h1>\n",$vote[1]);
				$choix = explode("\r\n",$vote[2]);
				foreach ($choix as $i => $c)
				{
			 		printf("<button id=choix%s>%s</button><br/>\n",$i,$c);
				}
				printf("<form method=POST><input type=submit value='Voter' onclick='sendVote(); return false;'></form>\n");
		                printf("</div>\n");
	        	        printf("<script>\n");
	                	printf("$('button').click(function(){\n");
		                printf("        $(this).toggleClass('selected');\n");
				if ($vote[3] == 1)
				{	
	        	        	printf("        $('button').not(this).removeClass('selected');\n");
				}
				printf("});\n");
				printf("function sendVote() {\n");
				printf("  x = '';\n");
				printf("  $('div#vote button.selected').each(function() { x+=$(this).attr('id'); x+=\"\\r\\n\"; });\n");
			       	printf("  $.ajax({\n");
			       	printf("       url : 'ajax.php',\n");
			       	printf("       type : 'POST',\n");
			       	printf("       data : 'action=vote&vote=%s&bulletin='+x,\n",$vote[0]);
			       	printf("       dataType : 'html',\n");
			       	printf("       success : function(code_html, statut){\n");
			       	printf("          $('#vote').remove();\n");
			       	printf("		 $(code_html).appendTo('#allComment');\n");
			       	printf("       },\n");
			       	printf("       error : function(resultat, statut, erreur){\n");
			       	printf("          alert('error');         \n");
			       	printf("       },\n");
			       	printf("       complete : function(resultat, statut){\n");
			       	printf("       }        \n");
			       	printf("    });\n");
				printf("}\n");
	             	 	printf("</script>\n");
			}
		}
		$query = "SELECT COUNT(*) FROM VOTE WHERE VOTE_ListeId=$reunion AND VOTE_Status=2 AND VOTE_Date > '".$datelimite."'";
		$nbVote = SQL($query,"RC");
		$datefin4 = $datelimite;
                if ($nbVote > 0)
                {
			$query = "SELECT VOTE_Id, VOTE_Question, VOTE_Reponses, VOTE_Type, VOTE_Date FROM VOTE WHERE VOTE_ListeId=$reunion AND VOTE_Status=2 AND VOTE_Date > '".$datelimite."'";
			$res = SQL($query);
			foreach ($res as $vote)
			{
				printf("<div id='resultat%s'>\n",$vote[0]);
				printf("<h3>%s</h3>\n",$vote[1]);
	                        if (isOwner())
        	                {
                	                print("<input class='remove' type='button' onclick='deleteResultat(".$vote[0].");' value='X'>\n");
                        	}

				$query="SELECT VOTE_Reponses FROM VOTE WHERE VOTE_Id=".$vote[0];
				$choix=SQL($query,"RC");
				$reponses = explode("\r\n",$choix);
				$resultats = Array();
				$count = 0;
				foreach ($reponses as $index => $reponse)
				{
					$query = "SELECT COUNT(*) FROM BULLETIN WHERE BULLETIN_VoteId=".$vote[0]." AND BULLETIN_Choix LIKE '%|".$index."|%'";
					$n = SQL($query,"RC");
					$resultats[$index] = $n;
					$count += $n;
				}
				if ($count > 0)
				{
					foreach ($resultats as $index => $res)
					{
						printf("<p><span class='label'>%s</span>\n",$reponses[$index]);
						printf("<span class='voies'>%s voies</span>\n",$res);
						printf("<span class='percent'>%0.1d %%</span>\n",$res/$count*100);
						printf("</p>\n");
					}
				}
				else
				{
					printf("<p>Aucun vote</p>\n");
				}
				printf("</div>\n");
				$datefin4 = $vote[4];
			}
		}
                $query = "SELECT COUNT(*) FROM VOTE WHERE VOTE_ListeId=$reunion AND VOTE_Status=3 AND VOTE_Date > '".$datelimite."'";
                $nbVote = SQL($query,"RC");
                $datefin5 = $datelimite;
                if ($nbVote > 0)
                {
			$query = "SELECT VOTE_Id, VOTE_Question, VOTE_Reponses, VOTE_Type, VOTE_Date FROM VOTE WHERE VOTE_ListeId=$reunion AND VOTE_Status=3 AND VOTE_Date > '".$datelimite."'";
                        $res = SQL($query);
                        foreach ($res as $vote)
                        {
                        	printf("<script>$('#resultat".$vote[0]."').remove();\n</script>\n");
                        	$datefin5 = $vote[4];
                        }
                }
		if ($datefin2 > $datefin1) $datefin1 = $datefin2;
		if ($datefin3 > $datefin1) $datefin1 = $datefin3; 
		if ($datefin4 > $datefin1) $datefin1 = $datefin4;
		if ($datefin5 > $datefin1) $datefin1 = $datefin5;
		addSession('datelimite',$datefin1);

	}

}
?>

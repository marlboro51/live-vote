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
		$procuId = getPost('voie',0);
		if ($voteid != 0 && $bulletin != '')
		{
			$query = "SELECT VOTE_Status FROM VOTE WHERE VOTE_Id='$voteid'";
			$status = SQL($query,"RC");

			$query = "SELECT COUNT(*) FROM PROCURATION WHERE PROCURATION_ListeId='$reunion' AND PROCURATION_GESRCId='$logged'";
			$aDonneProcu = SQL($query,"RC");

			$query = "SELECT PROCURATION_GESRCId FROM PROCURATION WHERE PROCURATION_ListeId='$reunion' AND PROCURATION_GEDSTId='$logged'";
			$procusRecues = SQL($query,"C");

			$query = "SELECT COUNT(*) FROM EMARGEMENT WHERE EMARGEMENT_GEId='$logged' AND EMARGEMENT_VoteId='$voteid' AND EMARGEMENT_Procuration='$procuId'";
			$voted = SQL($query,"RC");

			if ($status != "1") {
				printf("<div class='error'>Ce vote n'est pas (plus) ouvert</div>\n");
			}
			else if ($voted >0) {
				printf("<div class='error'>Vous avez d&eacute;j&agrave; vot&eacute;!</div>\n");
			} else if ($aDonneProcu> 0) {
				printf("<div class='error'>Vous ne pouvez pas voter car vous avez donn&eacute; une procuration! </div>\n");
			} else if ($procuId != 0 && !in_array($procuId,$procusRecues)) {
				printf("<div class='error'>Procuration invalide</div>\n");
			} else {	
				$select = explode("\r\n",$bulletin);
				$b = "|";
				foreach($select as $s)
				{
					if ($s != "") $b .= $s . '|';
				}
				$query = "INSERT INTO BULLETIN SET BULLETIN_VoteId='$voteid', BULLETIN_Choix='$b'";
				SQL($query);
				$query = "INSERT INTO EMARGEMENT SET EMARGEMENT_GEId='$logged', EMARGEMENT_VoteId='$voteid', EMARGEMENT_Procuration='$procuId'";
				SQL($query);
				printf("<div class='ok'>Vote pris en compte</div>\n");
			}
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

		$query = "SELECT COUNT(*) FROM VOTE, GE_LISTE WHERE GELISTE_ListeId=VOTE_ListeId AND GELISTE_GEId=$logged AND VOTE_ListeId=$reunion AND VOTE_Status=1 AND VOTE_Date > '".$datelimite."'";
		$nbVote = SQL($query,"RC");
		if ($nbVote > 0)
		{
			$query = "SELECT VOTE_Id, VOTE_Question, VOTE_Reponses, VOTE_Type, VOTE_Date FROM VOTE WHERE VOTE_ListeId=$reunion AND VOTE_Status=1 AND VOTE_Date > '".$datelimite."'";
			$res = SQL($query,"");

			$query = "SELECT 0, GE_NumFFS, GE_Nom, GE_Prenom FROM GE WHERE GE_Id='$logged'";			
			$moi = SQL($query,"R");

                        function addVote($v,$procu)
                        {
                                        $divVoteId = sprintf("vote%s_%s",$v[0],$procu[0]);
                                        printf("<div id=%s>\n",$divVoteId);
                                        printf("<h1>%s</h1>\n",$v[1]);
                                        $choix = explode("\r\n",$v[2]);
                                        foreach ($choix as $i => $c)
                                        {
                                                printf("<button class='%s %s' id=choix%s_%s_%s val=%s>%s</button><br/>\n",$divVoteId,($v[3]==2)?"multi":"single",$v[0],$procu[0],$i,$i,$c);
                                        }
                                        printf("<form method=POST><input type=submit value='Voter ([%s] %s %s)' onclick='sendVote(\"%s\",\"%s\",\"%s\"); return false;'></form>\n",$procu[1],$procu[2],$procu[3],$divVoteId,$v[0],$procu[0]);
                                        printf("</div>\n");
                                        printf("<script>\n");
                                        printf("$('button.multi.$divVoteId').click(function(){\n");
                                        printf("        $(this).toggleClass('selected');\n");
                                        printf("});\n");
                                        printf("$('button.single.$divVoteId').click(function(){\n");
                                        printf("        $(this).toggleClass('selected');\n");
                                        printf("        $('button.single.$divVoteId').not(this).removeClass('selected');\n");
                                        printf("});\n");
                                        printf("</script>\n");

                        }
			foreach ($res as $vote)
			{

				$datefin3 = $vote[4];

				$query = "SELECT PROCURATION_GESRCId, GE_NumFFS, GE_Nom, GE_Prenom FROM PROCURATION, GE WHERE PROCURATION_GESRCId=GE_Id AND  PROCURATION_ListeId='$reunion' AND PROCURATION_GEDSTId='$logged'";
				$procusRecues = SQL($query,"");

				addVote($vote,$moi);
				foreach ($procusRecues as $procu) // ($voie=0; $voie<=$procusRecues; $voie++)
				{
					addVote($vote,$procu);
				}
				printf("<script>\n");
				printf("function sendVote(divVoteId,vote,voie) {\n");
				printf("  x = '';\n");
				printf("  $('div#'+divVoteId+' button.selected').each(function() { x+=$(this).attr('val'); x+=\"\\r\\n\"; });\n");
			       	printf("  $.ajax({\n");
			       	printf("       url : 'ajax.php',\n");
			       	printf("       type : 'POST',\n");
			       	printf("       data : 'action=vote&vote='+vote+'&voie='+voie+'&bulletin='+x,\n");
			       	printf("       dataType : 'html',\n");
			       	printf("       success : function(code_html, statut){\n");
			       	printf("          $('#'+divVoteId).remove();\n");
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
						printf("<span class='voies'>%s voix</span>\n",$res);
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

<html>
<head>
<meta charset="utf-8" />
</head>
<script src="jquery.js"></script>
<link rel="stylesheet" href="style.css" type="text/css" />
<body>
<?php
include('common.php');

$logged = getSession('login',0);

$action = getPost('action','');
switch ($action)
{
case 'addVote':
	$question = getPost('question','');
	$reponses = getPost('reponses','');
	$reunion = getSession('reunion',0);
	$multi = getPost('multi',0);

	if ($question != '' && $reponses!='' && $reunion >0)
	{
		$type=0;
		if ($multi == 'on') $type=1;
		$query = "INSERT INTO VOTE SET VOTE_Question='".mysqli_real_escape_string($GDB["LINK"],$question)."', VOTE_Reponses='".mysqli_real_escape_string($GDB["LINK"],$reponses)."', VOTE_ListeId=$reunion, VOTE_Type=$type, VOTE_Status=1, VOTE_Date=now()";
		SQL($query);
	}
	else
	{
		printf("Il manque des &eacute;l&eacute;ments!");
	}
	break;
case 'cloture':
	$voteid = getPost('vote',0);
	$reunion = getSession('reunion',0);
	if ($voteid > 0 && $reunion > 0)
	{
		$query = "UPDATE VOTE SET VOTE_Status=2, VOTE_DATE=now() WHERE VOTE_Id=$voteid";
		SQL($query);
	}
	break;
}


if ($logged <= 0)
{
	printf("Impossible de cr&eacute;er un vote sans &ecirc;tre connect&eacute;\n");
}
else
{
	$reunion = getSession('reunion',0);
	displayLogoutForm();
	if ($reunion <= 0)
	{
		printf("Impossible de cr&eacute;er un vote sans choisir de reunion\n");
	}
	else
	{
		displayTitre();
		if (! isOwner())
		{
			printf("Impossible de cr&eacute;er un vote sans &ecirc;tre propri&eacute;taire de la r&eacute;union\n");
		}
		else
		{
			printf("<div id='addComment'>\n");
			printf("<form method=POST>\n");
			printf("<input type=hidden name=action value=addVote>\n");
			printf("<textarea name='question'></textarea><br/>\n");
			printf("<textarea name='reponses'></textarea><br/>\n");
			printf("<label for=multi>Choix multiple</label><input type='checkbox' name='multi'><br/>\n");
                        printf("<input type=submit value='Cr&eacute;er'>\n");
			printf("</form>\n");
			printf("</div><div id='allComment'>\n");
			$query = "SELECT VOTE_Id, VOTE_Question FROM VOTE WHERE VOTE_Status=1 AND VOTE_ListeId=$reunion";
			$votes = SQL($query,"");
			$query = "SELECT COUNT(*) FROM GE_LISTE WHERE GELISTE_ListeId='$reunion'";
			$totalge = SQL($query,"RC");
			foreach ($votes as $vote)
			{
				printf("<div><p>%s</p>",$vote[1]);
				printf("<form method=POST><input type=hidden name='action' value='cloture'>\n");
				printf("<input type=hidden name='vote' value='%s'><input type=submit value='Cloturer'></form>\n",$vote[0]);
				$query = "SELECT COUNT(*) FROM BULLETIN WHERE BULLETIN_VoteId='$vote[0]'";
				$bul = SQL($query,"RC");
				$query = "SELECT COUNT(*) FROM EMARGEMENT WHERE EMARGEMENT_VoteId='$vote[0]'";
				$procs = SQL($query,"RC");
				printf("<div class='result' id='part_%s'><span class=nb>%s</span>(<span class='procus'>%s</span>)/<span class=total>%s</span></div>\n",$vote[0],$bul,$procs,$totalge);
				$query = "select GE_NumFFS, GE_Nom, GE_Prenom, COUNT(*) FROM GE, EMARGEMENT WHERE GE_Id=EMARGEMENT_GEId AND EMARGEMENT_VoteId='$vote[0]' GROUP BY GE_NumFFS ORDER BY GE_Nom, GE_Prenom";
				$votants = SQL($query,"");
				foreach ($votants as $v) {
					printf("[%s] %s %s (%s)<br/>\n",$v[0],$v[1],$v[2],$v[3]);
				}
				printf("</div>\n");
			}
			printf("</div>\n");

		      	printf("<script>\n");
	       		printf("function launchAjax() {\n");

			printf("    $.ajax({\n");
       			printf("       url : 'ajax.php',\n");
		        printf("       type : 'POST',\n");
		        printf("       data : 'action=getVote',\n");
		        printf("       dataType : 'html',\n");
		        printf("       success : function(code_html, statut){\n");
			printf("         ");
		        printf("       },\n");

		       printf("       error : function(resultat, statut, erreur){\n");
		       printf("          alert('error');         \n");
		       printf("       },\n");

		       printf("       complete : function(resultat, statut){\n");
		       printf("       }        \n");

		       printf("    });\n");

		       printf("};\n");
		       printf("launchAjax(); setInterval(launchAjax,5000);\n");
		       printf("</script>\n");
		}
	}
}
?>
</body>
</html>

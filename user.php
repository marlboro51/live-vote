<html>
<head>
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
case 'login':
        $logged = checkLogin(getPost('login',''),getPost('mdp',''));
        if ($logged > 0)
                addSession('login',$logged);
        break;

case 'logout':
        unset($_SESSION['login']);
        unset($_SESSION['reunion']);
        $logged = 0;
        break;
case 'connect':
        $reunion = getPost('reunion',0);
        addSession('reunion',$reunion);
        break;

case 'importUser':
	$liste=getPost('users','');
	$users = explode("\r\n",$liste);
	foreach ($users as $i=> $user)
	{
		$data = explode("\t",$user);
		if (count($data) >= 5)
		{
			$query = "SELECT GE_Id FROM GE WHERE GE_NumFFS = '$data[3]'";
			$geids = SQL($query,"C");
			if (!isset($geids[0]))
			{
				$pwd = genereMdp(8);
				$query = "INSERT INTO GE SET GE_Nom='$data[0]', GE_Prenom='$data[1]', GE_Mail='$data[2]', GE_NumFFS='$data[3]', GE_Titre='$data[4]', GE_MotDePasse=PASSWORD('$pwd')";
				$geid = SQL($query);
				$message = "";
				$message .= "Voici vos identifiants de connexion pour les votes en ligne<br/>\n";
				$message .= sprintf(" - identifiant (n&deg; FFS) : %s<br/>\n",$data[3]);
				$message .= sprintf(" - mot de passe : %s<br/>\n",$pwd);
				$message .= sprintf("Lien vers l'interface de vote : <a href='http://speleos.eu/vote/?action=login&login=%s&mdp=%s'>speleos.eu/vote</a><br/>\n",$data[3],$pwd);
				$message .= "Restez connect&eacute; pendant l'AG les votes s'afficheront automatiquement au fur et &agrave; mesure.<br/>\n";
				$message .= "Si vous avez donn&eacute; une procuration, ignorez ce mail. Si vous en avez re&ccedil;u vous aurez plusieurs voix<br/>\n";
				$message .= "en cas de question : vote@speleos.eu, merci<br/>\n";
				$message .= "Bonne reunion<br/>\n<br/>\n";
				$message .= "L'equipe informatique";
				envoiMail($data[2],$data[0]." ".$data[1],$MAIL_VOTE, $MAIL_VOTE_NOM, "[VOTE FFS] nouvel acces", $message);
			}
			else
			{
				$geid = $geids[0];
			}
			$query = "SELECT COUNT(*) FROM MANAGE WHERE MANAGE_GEId='$geid' AND MANAGE_OwnerId='$logged'";
			$manage = SQL($query,"RC");
			if ($manage == 0)
			{
				$query = "INSERT INTO MANAGE SET MANAGE_GEId='$geid', MANAGE_OwnerId='$logged'";
				SQL($query);
			}
		}
	}
	break;
case 'affecte':
	$listeid = getSession('reunion',0);
	if ($listeid > 0)
	{
		$query = "DELETE FROM GE_LISTE WHERE GELISTE_ListeId='".$listeid."'";
		SQL($query);
		foreach ($_POST['users'] as $userid)
		{
			$query = "SELECT COUNT(*) FROM GE_LISTE WHERE GELISTE_GEId='$userid' AND GELISTE_ListeId='$listeid'";
			$nb = SQL($query,"RC");
			if ($nb == 0)
			{
				$query="INSERT INTO GE_LISTE SET GELISTE_GEId='$userid', GELISTE_ListeId='$listeid'";
				SQL($query);
			}
		}
		// supprimer les procurations impossibles.
		$query = "DELETE FROM PROCURATION WHERE PROCURATION_ListeId = '$listeid' AND (NOT EXISTS (SELECT * FROM GE_LISTE WHERE GELISTE_ListeId = PROCURATION_ListeId AND GELISTE_GEId = PROCURATION_GESRCId ) OR NOT EXISTS (SELECT * FROM GE_LISTE WHERE GELISTE_ListeId = PROCURATION_ListeId AND GELISTE_GEId = PROCURATION_GEDSTId))";
		SQL($query);
	}
	break;
}

if ($logged <= 0)
{
        unset($_SESSION['reunion']);
        $reunion=0;
        displayLoginForm();
}
else
{
        $reunion = getSession('reunion',0);
        displayLogoutForm();
        if ($reunion <= 0)
        {
                displayReunionsList();
        }
        else
        {
		displayTitre();
			printf("<div id='addComment'>\n");

                        printf("<input type='button' value='&lt;&lt; Retour' onclick='document.location.replace(&quot;.&quot;)'><br/>");

			printf("<form method=POST>\n");
			printf("<input type=hidden name=action value=importUser>\n");
			printf("<textarea name='users'></textarea><br/>\n");
                        printf("<input type=submit value='Ajouter'>\n");
			printf("</form>\n");

			printf("<input type='button' value='G&eacute;rer les procurations' onclick='document.location.replace(&quot;procuration.php&quot;)'><br/>");

			$query = "SELECT LISTE_Id, LISTE_Nom FROM LISTE, OWNER WHERE OWNER_GEId = '".$logged."' AND  OWNER_ListeId=LISTE_Id order by LISTE_Nom";
			$listes = SQL($query,"");
			$query = "SELECT  GE_Id, GE_Nom, GE_Prenom, GE_Mail, GE_NumFFS, GE_Titre, sum(GELISTE_ListeId='".$reunion."') as isInListe FROM (SELECT DISTINCT GE_Id, GE_Nom, GE_Prenom, GE_Mail, GE_NumFFS, GE_Titre FROM GE, MANAGE WHERE GE_Id=MANAGE_GEId AND MANAGE_OwnerId='".$logged."') as sel LEFT JOIN GE_LISTE ON sel.GE_Id=GE_LISTE.GELISTE_GEId group by  GE_Id order by GE_Nom, GE_Prenom";
			$users = SQL($query,"");
			printf("<div class='tab'><form method=POST>\n");
			printf("<input type='hidden' name='action' value='affecte'>\n");
			foreach ($users as $user)
			{
				$txt = sprintf("[%s] %s %s &lt;%s&gt; - %s", $user[4], $user[1], $user[2], $user[3], $user[5]);
				$txt = sprintf("%s %s <%s>\n",$user[1],$user[2], $user[3]);
				printf("<input type='checkbox' name='users[]' value='%s' %s><label class='ligne'  for='%s'>%s</label><br/>\n",$user[0],($user[6]>0)?"checked='checked'":"",$user[0],$txt);
			}
			printf("<input type=submit value='Affecter'>\n");
			printf("</div></form>\n");
			printf("</div>\n");
	}
}
?>
</body>
</html>

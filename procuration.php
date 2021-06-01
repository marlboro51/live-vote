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
        $logged = checkLogin(getPOST('login',''),getPost('mdp',''));
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
	break;
case 'affecte':
	$listeid = getSession('reunion',0);
	if ($listeid > 0)
	{
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

                        printf("<input type='button' value='&lt;&lt; Retour' onclick='document.location.replace(&quot;user.php&quot;)'><br/>");


			$query = "SELECT procu.PROCURATION_Id, src.GE_Nom, src.GE_Prenom, src.GE_NumFFS, dst.GE_Nom, dst.GE_Prenom, dst.GE_NumFFS FROM PROCURATION procu LEFT JOIN GE src ON procu.PROCURATION_GESRCId=src.GE_Id LEFT JOIN GE dst ON procu.PROCURATION_GEDSTId=dst.GE_Id WHERE PROCURATION_ListeId='$reunion' ORDER BY dst.GE_Nom, dst.GE_Prenom, src.GE_Nom, src.GE_Prenom";
			$procus = SQL($query);

			printf("<div class='tab'>\n");
                        foreach ($procus as $procu)
			{
				printf("<span class='nom'>[%s] %s %s</span> =&gt; <span class='nom'>[%s] %s %s</span><br/>",$procu[3],$procu[1],$procu[2],$procu[6],$procu[4],$procu[5]);
				//TODO add delete sur $procu[0]
			}
			printf("</div>\n");

			printf("<div class='tab'><form method=POST>\n");
			printf("<input type='hidden' name='action' value='procure'>\n");
                        
			$querySrc = "SELECT GE_Id, GE_Nom, GE_Prenom, GE_NumFFS, COUNT(PROCURATION_Id <> 'NULL') AS src  FROM GE JOIN GE_LISTE ON GELISTE_GEId=GE_Id LEFT JOIN  PROCURATION ON GE_Id=PROCURATION_GESRCId WHERE GELISTE_ListeId='$reunion' GROUP BY GE_Id";
			$queryDst = "SELECT GE_Id, GE_Nom, GE_Prenom, GE_NumFFS, COUNT(PROCURATION_Id <> 'NULL') AS dst  FROM GE JOIN GE_LISTE ON GELISTE_GEId=GE_Id LEFT JOIN  PROCURATION ON GE_Id=PROCURATION_GEDSTId WHERE GELISTE_ListeId='$reunion' GROUP BY GE_Id";

			$query = "SELECT ge.GE_Id, ge.GE_Nom, ge.GE_Prenom, ge.GE_NumFFS, s.src, d.dst FROM GE ge JOIN GE_LISTE  ON ge.GE_Id=GELISTE_GEId JOIN ($querySrc) as s ON s.GE_Id=ge.GE_Id JOIN ($queryDst)as d ON d.GE_Id=ge.GE_Id WHERE GELISTE_ListeId='$reunion'";

			$geprocus = SQL($query);

			printf("<select name='gesrc'>\n");
			foreach ($geprocus as $src)
			{
				if ($src[4]=='0' && $src[5]=='0')
					printf("<option value='%s'>[%s] %s %s</option>\n",$src[0],$src[3],$src[1],$src[2]);
			}
			printf("</select>\n");

			printf("=&gt;</br>\n");


			printf("<select name='gedst'>\n");
                        foreach ($geprocus as $dst)
                        {
                                if ($dst[4]=='0' && $dst[5]<2)
					printf("<option value='%s'>[%s] %s %s</option>\n",$dst[0],$dst[3],$dst[1],$dst[2]);
			}
			printf("</select>\n");

			printf("<input type=submit value='Ajouter procuration'>\n");
			printf("</form>\n");
			printf("</div>\n");
	}
}
?>
</body>
</html>

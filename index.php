<html>
<head>
<meta charset="utf-8" />
</head>
<script src="jquery.js"></script>
<link rel="stylesheet" href="style.css" type="text/css" />
<body>
<div id="logo"><img src="https://csr-occitanie.fr/wp-content/uploads/2019/03/logo-csr-o-e1553786551757.png"></div>
<?php
include('common.php');

$logged = getSession('login',0);

$action = getPost('action','',true);
switch ($action)
{
case 'login':
	$logged = checkLogin(getPost('login','',true),getPost('mdp','',true));
	if ($logged > 0) 
	{
		addSession('login',$logged);
		logLogin($logged);
	}
	else
	{
		$login = getPost('login','',true);
		$mdp = getPost('mdp','',true);
		$query = "INSERT INTO ERROR SET ERROR_Login='".$login."', ERROR_Mdp='".$mdp."', ERROR_Date=now()";
		SQL($query);
		printf("<div class='error'>Identifiants incorrects<br/>L'identifiant est le num&eacute;ro de licence FFS sous la forme A12-123-123<br/>Conseil : faites des copier/coller depuis le mail d'invitation pour &eacute;viter les erreurs de copie.</div>\n");
	}
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

case 'comment':
	$comment = getPost('comment','');
	if ($comment != '')
	{
		addComment($comment);
	}
	break;

case 'create':
	$nom = getPost('reunion','');
	if ($nom != '')
	{
		addReunion($nom);
	}

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

	$query = "SELECT GE_NumFFS, GE_Nom, GE_Prenom FROM GE WHERE GE_Id='$logged'";
	$infos = SQL($query,"R");
	$query = "SELECT COUNT(*) FROM PROCURATION WHERE PROCURATION_GEDSTId='$logged' AND PROCURATION_ListeId='$reunion'";
	$nbProc = SQL($query,"RC");
	printf("[%s] %s %s - %s procuration%s<br/>\n",$infos[0],$infos[1],$infos[2],$nbProc,($nbProc>1)?"s":"");

	displayLogoutForm();
	if ($reunion <= 0)
	{
		displayReunionsList();
	}
	else
	{
		displayTitre();
		displayComment();
		launchAjax();
	}
	if (isOwner())
	{
		printf("<input type=button value='Lancer un vote' onclick='window.open(\"vote.php\")'><br/>\n");
		printf("<input type=button value='Liste des &eacute;lecteurs' onclick='document.location.replace(\"user.php\")'><br/>\n");
//		printf("<div id='user'></div>");
	}
}
?>
</body>
</html>

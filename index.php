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

case 'comment':
	$comment = getPOST('comment','');
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

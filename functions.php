<?php
include('bases.php');
require_once 'class.phpmailer.php';
function getPost($action,$default)
{
	if (isset($_POST[$action]))
		return $_POST[$action];
	else
		return $default;
}
function addSession($key,$value)
{
	$_SESSION[$key] = $value;
}
function getSession($key,$default) 
{
	if (isset($_SESSION[$key]))
		return $_SESSION[$key];
	else 
		return $default;
}
function displayLoginForm()
{
	printf("<form method=POST>\n");
	printf("  <label>Identifiant</label><input type='text' name='login'><br/>\n");
	printf("  <label>Mot de passe</label><input type='password' name='mdp'><br/>\n");
	printf("  <input type='hidden' name='action' value='login'>\n");
	printf("  <input type='submit' value='Se connecter'>\n");
	printf("</form>\n");
}
function displayLogoutForm()
{
	printf("<form method=POST action='.'>\n");
        printf("  <input type='hidden' name='action' value='logout'>\n");
        printf("  <input type='submit' value='Se d&eacute;connecter'>\n");
        printf("</form>\n");

}
function checkLogin($login,$mdp)
{
	$query = "SELECT GE_Id FROM GE WHERE GE_NumFFS='".$login."' AND GE_MotDePasse=PASSWORD('".$mdp."')";
	return SQL($query,"RC");

}
function isAdmin()
{
	$logged = getSession('login',0);
	if  ($logged > 0)
        {
                $query = "SELECT count(*) FROM GE WHERE GE_Id='$logged' and GE_Titre='admin'";
		if (SQL($query,"RC") != "0")
                        return true;
                else
                        return false;
        }
        return false;
}
function isOwner() 
{
        $logged = getSession('login',0);
        $reunion = getSession('reunion',0);
        if ($logged > 0 && $reunion > 0)
	{
		$query = "SELECT count(*) FROM OWNER WHERE OWNER_GEId='$logged' AND OWNER_ListeId='$reunion'";
		if (SQL($query,"RC") != "0")
			return true;
		else
			return false;
	}
	return false;
}
function displayReunionsList()
{
	$logged = getSession('login',0);
	$query = "SELECT LISTE_Id, LISTE_Nom from LISTE,  GE_LISTE WHERE LISTE_Id=GELISTE_ListeId and GELISTE_GEId='".$logged."'";
	$query = "select LISTE_Id, LISTE_Nom from LISTE, (select GELISTE_ListeId ListeId from GE_LISTE where GELISTE_GEId = '".$logged."' union select OWNER_ListeId from OWNER where OWNER_GEId = '".$logged."') as go where go.ListeId=LISTE_Id order by LISTE_Id";
	$reus = SQL($query,"");

	foreach ($reus as $reu)
	{
		printf("<form method=POST><input type='hidden' name='action' value='connect'><input type='hidden' name='reunion' value='%s'><input type='submit' value='%s'></form><br/>\n",$reu[0],$reu[1]);
	}

	if (isAdmin())
	{
		printf("<form method=POST><input type='hidden' name='action' value='create'><input type='text' name='reunion' /><input type='submit' value='Ajouter'></form>\n");
	}
}
function addReunion($nom)
{
	global $GDB;
	$logged = getSession('login',0);
	$query = "INSERT INTO LISTE SET LISTE_Nom='".mysqli_real_escape_string($GDB["LINK"],$nom)."'";
	$reunion = SQL($query);
	$query = "INSERT INTO OWNER SET OWNER_GEId='$logged', OWNER_ListeId='$reunion'";
	SQL($query);
}
function displayTitre()
{
	$reunion = getSession('reunion',0);
	if ($reunion == 0) return;
	$query="SELECT LISTE_Nom  FROM LISTE WHERE LISTE_Id='".$reunion."'";
	$titre = SQL($query,"RC");
	printf("<h1>%s</h1>\n",$titre);
}
function displayComment()
{
	printf("<div id='allComment'></div>\n");
	printf("<script>function sendcomment() { \n");
	printf("    $.ajax({\n");
       printf("       url : 'ajax.php',\n");
       printf("       type : 'POST',\n");
       printf("       data : 'action=sendcomment&comment='+$('#comment').val(),\n");
       printf("       dataType : 'html',\n");
       printf("       success : function(code_html, statut){\n");
       printf("          $('#comment').val('');\n");
       printf("       },\n");
       printf("       error : function(resultat, statut, erreur){\n");
       printf("          alert('error');         \n");
       printf("       },\n");
       printf("       complete : function(resultat, statut){\n");
       printf("       }        \n");
       printf("    });\n");
       printf(" }\n");

       printf("function deleteComment(x) {\n");
       printf("  $.ajax({\n");
       printf("       url : 'ajax.php',\n");
       printf("       type : 'POST',\n");
       printf("       data : 'action=deletecomment&comment='+x,\n");
       printf("       dataType : 'html',\n");
       printf("       success : function(code_html, statut){\n");
       printf("          $('#comment'+x).remove();\n");
       printf("       },\n");
       printf("       error : function(resultat, statut, erreur){\n");
       printf("          alert('error');         \n");
       printf("       },\n");
       printf("       complete : function(resultat, statut){\n");
       printf("       }        \n");
       printf("    });\n");
       printf("}\n");

       printf("function deleteResultat(x) {\n");
       printf("  $.ajax({\n");
       printf("       url : 'ajax.php',\n");
       printf("       type : 'POST',\n");
       printf("       data : 'action=deleteresultat&resultat='+x,\n");
       printf("       dataType : 'html',\n");
       printf("       success : function(code_html, statut){\n");
       printf("          $('#resultat'+x).remove();\n");
       printf("       },\n");
       printf("       error : function(resultat, statut, erreur){\n");
       printf("          alert('error');         \n");
       printf("       },\n");
       printf("       complete : function(resultat, statut){\n");
       printf("       }        \n");
       printf("    });\n");
       printf("}\n");
       printf("</script>\n");

	if (true)
	{
		printf("<div id='addComment'><form method=POST onsubmit='sendcomment();return false;'>\n");
		printf("<p>Envoyer un message : <br/>\n");
		printf("<textarea id=comment></textarea><br/>\n");
	        printf("<input type='hidden' name='action' value='comment'>\n");
	        printf("<input type='submit' value='Envoyer'>\n");
		printf("</form></div>");
	}
	unset($_SESSION['datelimite']);
}
function addComment($msg)
{
	global $GDB;
	$logged = getSession('login',0);
	$reunion = getSession('reunion',0);
	if ($logged > 0 && $reunion > 0)
	{
		$query = "SELECT CONCAT(GE_Nom, ' ', GE_Prenom) FROM GE WHERE GE_Id='".$logged."'";
		$auteur = SQL($query,"RC");
		$query = "INSERT INTO MESSAGE SET MESSAGE_Txt='".mysqli_real_escape_string($GDB["LINK"],$msg)."', MESSAGE_Auteur='".$auteur."', MESSAGE_Date=now(), MESSAGE_Status=1, MESSAGE_ReunionId='".$reunion."'";
		SQL($query);
	}
}
function deleteComment($msg)
{
	$logged = getSession('login',0);
        $reunion = getSession('reunion',0);
        if ($logged > 0 && $reunion > 0)
        {
               $query = "UPDATE MESSAGE SET MESSAGE_Status=0, MESSAGE_Date=now()  WHERE MESSAGE_Id=".$msg." and  MESSAGE_ReunionId='".$reunion."'";
               SQL($query);
        }

}
function deleteResultat($x)
{
        $logged = getSession('login',0);
        $reunion = getSession('reunion',0);
        if ($logged > 0 && $reunion > 0)
        {
               $query = "UPDATE VOTE SET VOTE_Status=3, VOTE_Date=now()  WHERE VOTE_Id=".$x." and  VOTE_ListeId='".$reunion."'";
               SQL($query);
        }

}
function launchAjax()
{
	printf("<script>\n");
	printf("function launchAjax() {\n");
     
       printf("    $.ajax({\n");
       printf("       url : 'ajax.php',\n");
       printf("       type : 'POST',\n");
       printf("       data : '',\n");
       printf("       dataType : 'html',\n");
       printf("       success : function(code_html, statut){\n");
       printf("          $(code_html).appendTo('#allComment');\n");
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

function genereMdp($longueur=8, $possible='abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789')
{
        $mdp = "";
        $pLen = strlen($possible)-1;
        while ($longueur--) 
        {
                $mdp .= $possible{mt_rand(0,$pLen)};
        }
        return $mdp;
}
function envoiMail($to,$tostr,$from,$fromstr,$subject,$message)
{
        $mailer = new PHPMailer(true);
        $error = "";
	try {
		$mailer->isSMTP();
		$mailer->Host = 'mail.gandi.net';
		$mailer->SMTPAuth = true;
		$mailer->Username = "vote@speleos.eu";
		$mailer->Password = "LpdVLd09!";
		$mailer->SMTPSecure = 'STARTTLS';	
		$mailer->Port = 587;

                $mailer->AddAddress($to,$tostr);
                $mailer->SetFrom($from,$fromstr);
                $mailer->Subject = $subject;
                $mailer->isHTML(true);
		$mailer->Body = $message;
                $mailer->Send();
        } catch (phpmailerException $e) {
                $error .= $to . ' : ' . $e->errorMessage();
        } catch (Exception $e) {
                $error .= $to . ' : ' . $e->getMessage();
        }
        return $error;
}


?>

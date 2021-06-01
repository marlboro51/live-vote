<?php
	include ("server.php");

	$DEBUG = 0;

	$SESSION = array(
		"TIMEOUT" => 600,
		"TIMEDIFFDIFF" => 60
	);

	function Error($code)
	{
		return die($code);
	}
	
	function getTableDate($tablename)
	{
		global $GDB;
		$link = mysqli_connect($GDB["HOSTNAME"], $GDB["USERNAME"], $GDB["PASSWORD"]) or Error("Unable to connect to MySQL Schema.");
		if (!mysqli_select_db($link,"information_schema")) return "unknown";
		$query = "select UPDATE_TIME from TABLES where TABLE_SCHEMA='".$GDB["DATABASE"]."' and TABLE_NAME='".$tablename."'";
		$queryRes = mysql_query($query, $link) or Error("Unable to query $query");
		$row = mysql_fetch_row($queryRes);
		$results = $row[0];
		mysql_free_result($queryRes) or Error("Unable to free results.");
		return $results;
	}

	switch (strtoupper($GDB["TYPE"]))
	{
		case "MYSQL" :
			$GDB["LINK"] = mysqli_connect($GDB["HOSTNAME"], $GDB["USERNAME"], $GDB["PASSWORD"], $GDB["DATABASE"]) or Error("Unable to connect to MySQL.");
//			mysqli_select_db($GDB["LINK"],$GDB["DATABASE"]) or Error("Unable to use database " . $GDB["DATABASE"] . ".");
			break;
		case "ORACLE" :
			Error("ORACLE interface not available.");
			break;
		default :
			Error("Unknown DBMS type : \"" . $GDB["TYPE"] . "\"");
	}


	function SQL($query, $mode = "")
	{
		global $GDB;
		$results = array();

		switch (strtoupper($GDB["TYPE"]))
		{
			case "MYSQL" :
				$queryRes = mysqli_query($GDB["LINK"],$query) or Error("Unable to query $query");
				if (preg_match("/select /i", $query)) 
				{
					switch (strtoupper($mode))
					{
						case "" :	// retourne un tableau tel que $t[$i][$j] vaut le champ $j de la ligne $i
							while ($row = mysqli_fetch_row($queryRes))
								array_push($results, $row);
							break;
						case "R" :	// retourne un tableau tel que $t[$i] vaut le $i-eme champ de la (seule) ligne 
							$results = mysqli_fetch_row($queryRes);
							break;
						case "C" :	// retourne un tableau tel que $t[$i] vaut le (seul) champ de la ligne $i
							while ($row = mysqli_fetch_row($queryRes))
								array_push($results, $row[0]);
							break;
						case "RC" :	// retourne une valeur scalaire
						case "CR" :
							$row = mysqli_fetch_row($queryRes);
							$results = $row[0];
							break;
					}
//					mysqli_free_result($queryRes) or Error("Unable to free results.");
				}
				if (preg_match("/insert /i", $query))
				{
					$results = mysqli_insert_id($GDB["LINK"]);
				}
				break;
			case "ORACLE" :
				Error("ORACLE interface not available.");
				break;
			default :
				Error("Unknown DBMS type : \"" . $GDB["TYPE"] . "\"");
		}
		return $results;
	}

	function SQLRowsAsHTML($queryRes)
	{
		$res = "";
		foreach ($queryRes as $v)
		{
			$res .= "<tr><td>" . implode($v, "</td><td>") . "</td></tr>";
		}
		return "<table bgcolor=\"#FFF0F0\" border=\"1\">$res</table>";
	}

?>

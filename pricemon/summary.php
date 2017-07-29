<?php
require "settings.cfg";

if(isset($_POST["rank_threshold"])) {
	$rank_threshold=$_POST["rank_threshold"];
}

if(isset($_POST["price_threshold"])) {
	$price_threshold=$_POST["price_threshold"];
}
?>

<HTML>
<HEAD>
<TITLE>Eratosthenes</TITLE>
<meta charset="utf-8">
</HEAD>
<BODY>

<?php

function check_mysql()
{
    if (mysql_errno() > 0)
    {
        die("<BR> MySQL error " . mysql_errno() . ": " . mysql_error());
    }
}

$db = mysql_connect("localhost", "Eratosthenes", "DEFAULT_PASSWORD_CHANGEIT");
 if (!$db)
 {
     die("Failed to open connection to MySQL server.");
 }

$table = 'Eratosthenes';
mysql_select_db("Eratosthenes");
check_mysql();

$SQLcommand = "SELECT ASIN, COUNT(*) from $table WHERE Price != '0.00'";
		$SQLresult = MYSQL_QUERY($SQLcommand);
    		check_mysql();
		$countrows=mysql_fetch_array($SQLresult);
		$numrows=$countrows[1];

$SQLcommand = "SELECT DISTINCT Title, ASIN, Price, AvgPrice, LowNewPrice, LowUsedPrice, NumberNew, NumberUsed, SalesRank from ".$table." WHERE Price != '0.00' ORDER BY BookCase ASC";
		$SQLresult = MYSQL_QUERY($SQLcommand);
    		check_mysql();
		
		$counter=1;
		$greenarr=array();
		$purplearr=array();
		$bluearr=array();
		$yellowarr=array();
		$redarr=array();
		$brownarr=array();
		$greyarr=array();
		$uncaughtarr=array();

		$green_hex='#006600';
		$purple_hex='#800080';
		$blue_hex='#0000FF';
		$yellow_hex='#FFCC00';
		$red_hex='#FF0000';
		$brown_hex='#660000';
		$grey_hex='#808080';
		$uncaught_hex='#404040';
		$green_font="<font color=\"".$green_hex."\">";
		$purple_font="<font color=\"".$purple_hex."\">";
		$blue_font="<font color=\"".$blue_hex."\">";
		$yellow_font="<font color=\"".$yellow_hex."\">";
		$red_font="<font color=\"".$red_hex."\">";
		$brown_font="<font color=\"".$brown_hex."\">";
		$grey_font="<font color=\"".$grey_hex."\">";
		$uncaught_font="<font color=\"".$uncaught_hex."\">";

		$lines = file($inventorycsv);
		while($row=mysql_fetch_array($SQLresult))
		{
			if($row['ASIN'] != "")
			{
				$foundit=0;
                        	//Check for books that are not in the Amazon inventory
                        	foreach ($lines as $line_num => $line)
                        	{
                                	$this_line=preg_split('/\s/',$line);
                                	if($this_line[1] == "asin") { continue; }
	
                                	//echo "Does ". $row['ASIN'] . " == " . $this_line[1] . "<br>";
                                	if($row['ASIN'] == $this_line[1])
                                	{
                                        	$foundit=1;
                                        	break;
                                	}
                        	}

                        	if($foundit == 0)
                        	{
                                	array_push($brownarr, "<tr><td><a href=\"http://www.amazon.com/dp/".$row['ASIN']."\" target=\"_blank\">".$brown_font.$row['Title']."</a></td><td><a href=\"inline_search.php?searchTerm=ASIN&searchVal=".$row['ASIN']."\" target=\"_blank\">".$brown_font."EDIT</a></tr>");
                                	continue;
                        	}

				$MyPrice = $row['Price'];
				$AvgPrice = $row['AvgPrice'];
				$NewPrice = $row['LowNewPrice'];
				$UsedPrice = $row['LowUsedPrice'];
				$NumNew = $row['NumberNew'];
				$NumUsed = $row['NumberUsed'];
				$SalesRank = $row['SalesRank'];

				$info="Lowest New Price: $NewPrice\n";
				$info.="Lowest Used Price: $UsedPrice\n";
				$info.="Average Price: \$$AvgPrice <=\t\t=> Your Price: \$$MyPrice\n";
				$info.="Number New: $NumNew\n";
				$info.="Number Used: $NumUsed\n";
				$info.="Sales Rank: $SalesRank";

				$LowThresh=(1 - ("." . $price_threshold));
                                $HighThresh=1 . "." . $price_threshold;
                                $LowestAllowed=($AvgPrice * $LowThresh);
                                $HighestAllowed=($AvgPrice * $HighThresh);
				if($SalesRank == 0 || $SalesRank == "")
				{
					// Make the link grey
					array_push($greyarr, "<tr><td><a href=\"http://www.amazon.com/dp/".$row['ASIN']."\" target=\"_blank\" title=\"".$info."\">".$grey_font.$row['Title']."</a></td><td><a href=\"inline_search.php?searchTerm=ASIN&searchVal=".$row['ASIN']."\" target=\"_blank\">".$grey_font."EDIT</a></tr>");
					
				}
                                elseif($SalesRank < $rank_threshold)
                                {
                                        if ($MyPrice < $LowestAllowed)
                                        {
                                                // Make the link purple
                                                array_push($purplearr, "<tr><td><a href=\"http://www.amazon.com/dp/".$row['ASIN']."\" target=\"_blank\" title=\"".$info."\">".$purple_font.$row['Title']."</a></td><td><a href=\"inline_search.php?searchTerm=ASIN&searchVal=".$row['ASIN']."\" target=\"_blank\">".$purple_font."EDIT</a></tr>");
                                        }
                                        elseif($MyPrice > $HighestAllowed)
                                        {
                                                // Make the link blue
                                                array_push($bluearr, "<tr><td><a href=\"http://www.amazon.com/dp/".$row['ASIN']."\" target=\"_blank\" title=\"".$info."\">".$blue_font.$row['Title']."</a></td><td><a href=\"inline_search.php?searchTerm=ASIN&searchVal=".$row['ASIN']."\" target=\"_blank\">".$blue_font."EDIT</a></tr>");

                                        }
                                        else
                                        {
                                                // Make the link green
                                                array_push($greenarr, "<tr><td><a href=\"http://www.amazon.com/dp/".$row['ASIN']."\" target=\"_blank\" title=\"".$info."\">".$green_font.$row['Title']."</a></td><td><a href=\"inline_search.php?searchTerm=ASIN&searchVal=".$row['ASIN']."\" target=\"_blank\">".$green_font."EDIT</a></tr>");
                                        }
                                }
				elseif($MyPrice > $AvgPrice && $SalesRank > $rank_threshold)
				{
					// Make the link red
					array_push($redarr, "<tr><td><a href=\"http://www.amazon.com/dp/".$row['ASIN']."\" target=\"_blank\" title=\"".$info."\">".$red_font.$row['Title']."</a></td><td><a href=\"inline_search.php?searchTerm=ASIN&searchVal=".$row['ASIN']."\" target=\"_blank\">".$red_font."EDIT</a></tr>");
				}
				elseif($MyPrice <= $AvgPrice && $SalesRank > $rank_threshold)
				{
					// Make the link yellow
					array_push($yellowarr, "<tr><td><a href=\"http://www.amazon.com/dp/".$row['ASIN']."\" target=\"_blank\" title=\"".$info."\">".$yellow_font.$row['Title']."</a></td><td><a href=\"inline_search.php?searchTerm=ASIN&searchVal=".$row['ASIN']."\" target=\"_blank\">".$yellow_font."EDIT</a></tr>");
				}
				else
				{
					// Uncaught!!!
					array_push($uncaughtarr, "<tr><td><a href=\"http://www.amazon.com/dp/".$row['ASIN']."\" target=\"_blank\" title=\"".$info."\">".$uncaught_font.$row['Title']."</a></td><td><a href=\"inline_search.php?searchTerm=ASIN&searchVal=".$row['ASIN']."\" target=\"_blank\">".$uncaught_font."EDIT</a></tr>");
				}
			$counter++;
			}

		}

		if(file_exists($inventorycsv)) {
			echo "Inventory was last uploaded on: " . date("F d Y H:i:s.", filemtime($inventorycsv));
			echo "<br><br>";
		}
		echo "<center>";
		echo "<table align=\"center\" width=85% border=\"1\">";
		echo "<tr><td><b>Category</b></td><td><b>Number of items</b></td><td><b>Percentage of stock</b></td></tr>";
		echo "<tr bgcolor=\"".$green_hex."\"><td>Perfect</td><td>".count($greenarr)."</td><td>".round(((count($greenarr) / $counter) * 100), 2)."</td></tr>";
		echo "<tr bgcolor=\"".$purple_hex."\"><td>Too Cheap</td><td>".count($purplearr)."</td><td>".round(((count($purplearr) / $counter) * 100), 2)."</td></tr>";
		echo "<tr bgcolor=\"".$blue_hex."\"><td>Too Expensive</td><td>".count($bluearr)."</td><td>".round(((count($bluearr) / $counter) * 100), 2)."</td></tr>";
		echo "<tr bgcolor=\"".$yellow_hex."\"><td>Unpopular</td><td>".count($yellowarr)."</td><td>".round(((count($yellowarr) / $counter) * 100), 2)."</td></tr>";
		echo "<tr bgcolor=\"".$red_hex."\"><td>Garbage</td><td>".count($redarr)."</td><td>".round(((count($redarr) / $counter) * 100), 2)."</td></tr>";
		echo "<tr bgcolor=\"".$brown_hex."\"><td>Not For Sale</td><td>".count($brownarr)."</td><td>".round(((count($brownarr) / $counter) * 100), 2)."</td></tr>";
		echo "<tr bgcolor=\"".$grey_hex."\"><td>Have No Rank</td><td>".count($greyarr)."</td><td>".round(((count($greyarr) / $counter) * 100), 2)."</td></tr>";
		if (count($uncaughtarr) > 0)
		{
			echo "<tr bgcolor=\"".$uncaught_hex."\"><td>UNKNOWN</td><td>".count($uncaughtarr)."</td><td>".round(((count($uncaughtarr) / $counter) * 100), 2)."</td></tr>";
		}
		echo "<tr><td><b>TOTAL</b></td><td><b>".(count($greenarr) + count($purplearr) + count($bluearr) + count($yellowarr) + count($redarr) + count($uncaughtarr))."</b></td><td><b>".round((((count($greenarr) / $counter) * 100) + ((count($purplearr) / $counter) * 100) + ((count($bluearr) / $counter) * 100) + ((count($yellowarr) / $counter) * 100) + ((count($redarr) / $counter) * 100) + ((count($uncaughtarr) / $counter) * 100)), 2)."</b></td></tr>";
		echo "</table>";

?>

<?php
if (isset($message))
{
    echo "<BR><BR>$message";
}
?>
</FORM>
</BODY>
</HTML>

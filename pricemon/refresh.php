<?php
$siteURL = $_SERVER['HTTP_HOST'];
?>

<HTML>
<HEAD>
<TITLE>Eratosthenes</TITLE>
<script type="text/javascript">
$(window).load(function(){

   // PAGE IS FULLY LOADED  
   // FADE OUT YOUR OVERLAYING DIV
   $('#overlay').fadeOut();

});
</script>
</HEAD>
<BODY>
<center><button onclick="window.location='https://<?php echo "$siteURL" ?>/Eratosthenes';">Home</button></center>

<div>
<FORM ENCTYPE="multipart/form-data" ACTION="uploader.php" METHOD="POST">
    <br>Upload inventory (to update price value monitoring):<br>
    <INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="1000000" />
    <INPUT NAME="uploaded_file" type="file" />
    <INPUT TYPE="submit" value="Upload" />
</FORM>
</div>

<div style="position: fixed; bottom:5px;left:5px;" id="overlay">
	<iframe src="progress.php" name="progress" frameBorder=0 seamless scrolling="no" width="100"></iframe>
</div>

<button style="position: fixed; bottom:5px;right:5px;" onclick="window.location='#';">Back to top</button>

<?php

include 'aws.phar';

// ApaiIO AWS setup
require_once "vendor/autoload.php";
use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\ApaiIO;
// Build the item lookup
use ApaiIO\Operations\Lookup;

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
$merchantID = 'DEFAULT_MERCHANTID_CHANGEIT';

function tolerate_lookup($myasin)
{

$conf = new GenericConfiguration();
$conf
    ->setCountry('com')
    ->setAccessKey('ACCESS_KEY_DEFAULT_CHANGEME')
    ->setSecretKey('SECRET_KEY_DEFAULT_CHANGEME')
    ->setAssociateTag('ASSOCIATE_TAG_DEFAULT_CHANGEME');

	$apaiIo = new ApaiIO($conf);
	$lookup = new Lookup();
	$lookup->setItemId($myasin);
	$lookup->setResponseGroup(array('OfferSummary', 'SalesRank'));

	try
	{
		// Perform the search, and dump result
		$response = $apaiIo->runOperation($lookup);
		$output = new SimpleXMLElement($response);
	}

	catch(Exception $e)
	{
		//echo "Failed to lookup ASIN=>'$myasin', sleeping...";
		sleep(4);
		// Perform the search, and dump result
		$output=tolerate_lookup($myasin);
	}

	return $output;
}

$SQLcommand = "SELECT ASIN, COUNT(*) from $table WHERE Price != '0.00'";
		$SQLresult = MYSQL_QUERY($SQLcommand);
    		check_mysql();
		$countrows=mysql_fetch_array($SQLresult);
		$numrows=$countrows[1];

$SQLcommand = "SELECT DISTINCT Title, ASIN, Price from ".$table." WHERE Price != '0.00' ORDER BY BookCase ASC";
		$SQLresult = MYSQL_QUERY($SQLcommand);
    		check_mysql();
		
		echo "<br><center>(Hover over links to see price information)</center><br>";
		echo "<table align=\"center\" width=85% border=\"1\">";
		echo "<tr><td><b>Title</b></td></tr>";
		$counter=1;
		while($row=mysql_fetch_array($SQLresult))
		{
			if($row['ASIN'] != "")
			{
				$output=tolerate_lookup($row['ASIN']);

				$NewPrice=$output->Items->Item->OfferSummary->LowestNewPrice->FormattedPrice ?: 0;
				$NumNew=$output->Items->Item->OfferSummary->TotalNew ?: 0;
				$UsedPrice=$output->Items->Item->OfferSummary->LowestUsedPrice->FormattedPrice ?: 0;
				$NumUsed=$output->Items->Item->OfferSummary->TotalUsed ?: 0;
				if(($NumNew + $NumUsed) != 0) {
					$AvgPrice=round(((str_replace('$', '', $NewPrice) * $NumNew) + (str_replace('$', '', $UsedPrice) * $NumUsed)) / ($NumNew + $NumUsed), 2, PHP_ROUND_HALF_DOWN);
				}
				else {
					$AvgPrice=0;
				}
				$MyPrice=$row['Price'];
				$SalesRank=$output->Items->Item->SalesRank;
if($MyPrice > $AvgPrice && $SalesRank > 400000)
{
	// Make the link red
	$hrefcolor='<font color="#FF0000">';
}
elseif($MyPrice > $AvgPrice)
{
	// Make the link yellow
	$hrefcolor='<font color="#FFCC00">';
}
else
{
	// Make the link green
	$hrefcolor='<font color="#006600">';
}

$info="Lowest New Price: $NewPrice
Lowest Used Price: $UsedPrice
Average Price: \$$AvgPrice <=\t\t=> Your Price: \$$MyPrice
Number New: $NumNew
Number Used: $NumUsed
Sales Rank: $SalesRank";

			echo "<tr><td><a href=\"http://www.amazon.com/dp/".$row['ASIN']."\" target=\"_blank\" title=\"".$info."\">".$hrefcolor.$row['Title']."</a></td><td><a href=\"inline_search.php?searchTerm=ASIN&searchVal=".$row['ASIN']."\" target=\"_blank\">".$hrefcolor."EDIT</a></tr>";

$ASIN = $row['ASIN'];
$SQLcommand1 = "UPDATE $table SET AvgPrice = '$AvgPrice', LowNewPrice = '$NewPrice', LowUsedPrice = '$UsedPrice', NumberNew = '$NumNew', NumberUsed = '$NumUsed', SalesRank = '$SalesRank' WHERE ASIN = '$ASIN'";

$SQLresult1 = MYSQL_QUERY($SQLcommand1);
check_mysql();
			}

			$mypercent=floor(($counter / $numrows) * 100);
			echo "<form action=\"progress.php\" target=\"progress\" method=\"get\">";
			echo "<input type=\"hidden\" name=\"mypercent\" value=\"".$mypercent."\">";
			echo "</form>";

			if(($counter % 10) == 0)
			{
			echo "<SCRIPT LANGUAGE=\"JavaScript\">document.forms[$counter].submit();</SCRIPT>";
			}
			$counter++;
			flush();
		}

?>

<center><button onclick="window.location='https://<?php echo "$siteURL" ?>/Eratosthenes';">Home</button></center>
<br>

<?php
if (isset($message))
{
    echo "<BR><BR>$message";
}
?>
</FORM>
</BODY>
</HTML>

<HTML>
<HEAD>
<TITLE>Eratosthenes</TITLE>
</HEAD>
<BODY>

<?php

include 'aws.phar';

// ApaiIO AWS setup
require_once "vendor/autoload.php";
use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\ApaiIO;
// Build the item lookup
use ApaiIO\Operations\Lookup;

function check_mysql_cron()
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
check_mysql_cron();
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

$SQLcommand = "SELECT DISTINCT Title, ASIN, Price from ".$table." WHERE Price != '0.00' ORDER BY BookCase ASC";
		$SQLresult = MYSQL_QUERY($SQLcommand);
    		check_mysql_cron();
		
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

				$ASIN = $row['ASIN'];
				$SQLcommand1 = "UPDATE $table SET AvgPrice = '$AvgPrice', LowNewPrice = '$NewPrice', LowUsedPrice = '$UsedPrice', NumberNew = '$NumNew', NumberUsed = '$NumUsed', SalesRank = '$SalesRank' WHERE ASIN = '$ASIN'";
#
				$SQLresult1 = MYSQL_QUERY($SQLcommand1);
				check_mysql_cron();
			}
		}

		include './Eratosthenes/pricemon/summary.php';

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

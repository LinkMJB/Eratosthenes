<?php

$siteURL = $_SERVER['HTTP_HOST'];

//if we have a file
if((!empty($_FILES["uploaded_file"])) && ($_FILES['uploaded_file']['error'] == 0)) 
{ 
   //if the file is a CSV file and it's size is less than 350Mb
   //the mime type of a csv is 'application/vnd.ms-excel'
   if (($_FILES["uploaded_file"]["type"] == "application/vnd.ms-excel") || ($_FILES["uploaded_file"]["type"] == "text/plain")) 
   {   
	//The upload folder must already exist - if it does not, create one before running this script
        $filename = 'inventory.csv';
	$newname = dirname(__FILE__).'/upload/'.$filename;

       
      //save the uploaded file to it's new place
 	$savefile = move_uploaded_file($_FILES['uploaded_file']['tmp_name'],$newname);
       
	//checks to see if the file was successfully saved
	if (!savefile) 
        {
             echo "Error: A problem occurred during file upload!";
		 exit;
        }
   } 
   else 
   {
         echo 'ERROR: file type -> `$_FILES["uploaded_file"]["type"]` is not allowed for upload';
	 exit;
   }
} 

//else we do not have a file
else 
{
    echo "Error: No file uploaded";
    exit;
}


//database info
$databasehost = "localhost";
$databasename = "Eratosthenes";
$databasetable = "Eratosthenes";
$databaseusername = "Eratosthenes";
$databasepassword = "DEFAULT_PASSWORD_CHANGEIT";
$lineseparator = "\n";
$csvfile = "upload/inventory.csv";

//checks to make sure the file exists before opening it
if(!file_exists($csvfile)) 
{
     echo "Error: File not found.\n";
     exit;
}

//since the file exists, it can be opened
$file = fopen($csvfile,"r");

//checks to make sure the file was opened properly
if(!$file) 
{
     echo "Error: could not open data file.\n";
     exit;
}

//checks to make sure the opened file is not empty
$size = filesize($csvfile);
if(!$size) 
{
     echo "Error: File is empty.\n";
     exit;
}

//stores the content of the opened file
$csvcontent = fread($file,$size);

//closes the file
fclose($file);

//connects to the database defined above
$db = mysql_connect($databasehost, $databasename, $databasepassword);

//checks to see if the connection was successful
if (!$db)
{
     die("Failed to open connection to MySQL server.");
}

//selects the appropriate database to add user info to
//if it fails, a mysql error is displayed
@mysql_select_db($databasename) or die(mysql_error());

//an index to loop through all lines
$lines = 1;
$uploadlines = 0;
//an array
$linearray = array();

foreach(split($lineseparator,$csvcontent) as $line) 
{
     //skips the first and second line
     //this is required due to the format of the csv file pulled off portal
     if($lines == 1 || $lines == 2) {}

     else
     {	
     	//each line is divided/exploded into smaller strings, a comma signifies the next string
	list($sku, $asin, $price, $quantity) = preg_split('/\s+/', $line);

	//trims whitespaces
     	$asin = trim($asin);
     	$price = trim($price);
     	$quantity = trim($quantity);

	//mysql command to insert a students info(last,first,& id) into the correct fields in the $databasetable
     	//$query = "insert into $databasetable(ASIN, PRICE) values('$asin','$price')";
	if($quantity != 0)
	{
     	$query = "update $databasetable set Price='$price' where ASIN='$asin'";
	$uploadlines++;
	}

    	@mysql_query($query);
     }
     
	//lines is incremented
	$lines++;
}

//close the database
@mysql_close($db);

echo "
<script language=\"JavaScript\">alert(\"Upload complete: Updated prices for $uploadlines items in the database.\")
window.location='https://".$siteURL."/Eratosthenes'
</script>
";

?>

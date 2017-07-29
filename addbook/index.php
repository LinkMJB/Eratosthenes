<?php
session_start();
$siteURL = $_SERVER['HTTP_HOST'];
?>

<HTML>
<HEAD>
<TITLE>Eratosthenes</TITLE>
</HEAD>
<BODY>
<FORM METHOD="POST" ACTION="index.php">

<?php

function check_mysql()
{
    if (mysql_errno() > 0)
    {
        die("<BR> MySQL error " . mysql_errno() . ": " . mysql_error());
    }
}

$db = mysql_connect("localhost", "Eratosthenes", "DEFAULT_PASSWORD_CHANGEME");
 if (!$db)
 {
     die("Failed to open connection to MySQL server.");
 }

$table = Eratosthenes;

mysql_select_db("Eratosthenes");
check_mysql();

$Title  = $_POST["Title"];
$Author  = $_POST["Author"];
$ISBN   = $_POST["ISBN"];
$BookCase   = $_POST["BookCase"];
$Shelf   = $_POST["Shelf"];
$ASIN   = $_POST["ASIN"];
$UniqueID = $_POST["UniqueID"];

$searchTerm = $_POST["searchTerm"];
$searchVal = $_POST["searchVal"];
$chosen = $_POST["chosen"];

$add    = $_POST["add"];
$update = $_POST["update"];
$delete = $_POST["delete"];
$dropdown = $_POST["dropdown"];
$cancel = $_POST["cancel"];

?>


<BR>
<BR>Title:
<BR><INPUT TYPE="TEXT" NAME="Title" VALUE="<?php echo $Title; ?>">
<BR>
<BR>Author:
<BR><INPUT TYPE="TEXT" NAME="Author" VALUE="<?php echo $Author; ?>">
<BR>
<BR>ISBN:
<BR><INPUT TYPE="TEXT" NAME="ISBN" VALUE="<?php echo $ISBN; ?>">
<BR>
<BR>BookCase:
<BR><INPUT TYPE="TEXT" NAME="BookCase" VALUE="<?php echo $BookCase; ?>">
<BR>
<BR>ShelfNumber:
<BR><INPUT TYPE="TEXT" NAME="Shelf" VALUE="<?php echo $Shelf; ?>">
<BR>
<BR>ASIN:
<BR><INPUT TYPE="TEXT" NAME="ASIN" VALUE="<?php echo $ASIN; ?>">
<BR>
<BR>
<br>

<?php
check_mysql();


if (isset($add))
{
    $safeTitle = addslashes($Title);
    $safeAuthor = addslashes($Author);
    $query = "INSERT INTO $table (Title, Author, ISBN, BookCase, Shelf, ASIN) VALUES ('$safeTitle', '$safeAuthor', '$ISBN', '$BookCase', '$Shelf', '$ASIN')";
    $result = mysql_query($query);
    check_mysql();
    $StudentPOS= mysql_insert_id();
    echo "<script language=\"javascript\"> alert(\"Record Added (BookCase=$BookCase), (Shelf=$Shelf)\")</script>";
    echo "<script language=\"javascript\"> window.location = \"https://".$siteURL."/Eratosthenes/addbook\" </script>";	
}

?>

<FORM ENCTYPE="multipart/form-data" ACTION="index.php" METHOD="POST">
<INPUT TYPE="SUBMIT" NAME="add" VALUE="Add Book">
<br>
<br>
</FORM>
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

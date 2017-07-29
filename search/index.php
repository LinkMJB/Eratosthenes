<?php
$siteURL = $_SERVER['HTTP_HOST'];
?>

<HTML>
<HEAD>
<TITLE>Eratosthenes</TITLE>
</HEAD>
<BODY>
<center><button onclick="window.location='https://<?php echo "$siteURL" ?>/Eratosthenes';">Home</button></center>
<button style="position: fixed; bottom:5px;right:5px;" onclick="window.location='#';">Back to top</button>
<FORM METHOD="POST" ACTION="index.php">

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

$table = Eratosthenes;
$merchantID = 'DEFAULT_MERCHANTID_CHANGEIT';

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
$sortBy = $_POST["sortBy"];
$orderBy = $_POST["orderBy"];
$chosen = $_POST["chosen"];

$browse = $_POST["runbrowse"];
$update = $_POST["update"];
$delete = $_POST["delete"];
$cancel = $_POST["cancel"];
?>

<?php
	if(!isset($chosen))
	{	
?>
	<table>
	<tr>
	<td>Search by:</td>
	<td>Search value:</td>
	<td>Sort by:</td>
	<td>Order by:</td>
	</tr>
	<tr>
	<td>
	<select name="searchTerm">
	<option value="Title">Title</option>
	<option value="Author">Author</option>
	<option value="ISBN">ISBN</option>
	<option value="BookCase">Book Case</option>
	<option value="Shelf">Shelf Number</option>
	<option value="ASIN">ASIN</option>
	</select>
	</td>
	<td>
	<INPUT TYPE="TEXT" NAME="searchVal">
	</td>
	<td>
	<select name="sortBy">
	<option value="Title">Title</option>
	<option value="Author">Author</option>
	<option value="ISBN">ISBN</option>
	<option value="BookCase">Book Case</option>
	</select>
	</td>
	<td>
	<select name="orderBy">
	<option value="ASC">Ascending</option>
	<option value="DESC">Descending</option>
	</select>
	</td>
	<td>
	<input type ="SUBMIT" NAME="runsearch" VALUE="Search">
	</td>
	</tr>
	<tr><td>OR</td></tr>
	<tr><td><INPUT TYPE="SUBMIT" NAME="runbrowse" VALUE="Browse"></td></tr>
	</table>

	<?php
		if($searchTerm != "" && isset($searchTerm))
		{
		echo "<input style=\"position: fixed; bottom:5px;left:5px;\" type =\"SUBMIT\" NAME=\"choosebook\" VALUE=\"Select Book\">";
		}

		if(isset($runbrowse) || isset($browse))
		{
			$SQLcommand = "SELECT UniqueID, Title, Author, ISBN, BookCase, Shelf, ASIN from ".$table." ORDER BY BookCase ASC";
			$SQLresult = MYSQL_QUERY($SQLcommand);

			echo "<table align=\"center\" width=85% border=\"1\">";
			echo "<tr><td></td><td><b>BookCase</b></td><td><b>Shelf</b></td><td><b>Title</b></td><td><b>Author</b></td><td><b>ISBN</b></td></tr>";
			while($row=mysql_fetch_array($SQLresult))
			{
				if($row[ASIN] != "")
				{
				echo "<tr><td><input type=\"radio\" name=\"chosen\" value=\"".$row[UniqueID]."\"></td><td>".$row[BookCase]."</td><td>".$row[Shelf]."</td><td><a href=\"http://www.amazon.com/dp/".$row[ASIN]."\" target=\"_blank\">".$row[Title]."</a></td><td>".$row[Author]."</td><td>".$row[ISBN]."</td></tr>";
				}
				else
				{
				echo "<tr><td><input type=\"radio\" name=\"chosen\" value=\"".$row[UniqueID]."\"></td><td>".$row[BookCase]."</td><td>".$row[Shelf]."</td><td>".$row[Title]."</td><td>".$row[Author]."</td><td>".$row[ISBN]."</td></tr>";
				}
			}
			echo "</table>";
			echo "<input style=\"position: fixed; bottom:5px;left:5px;\" type =\"SUBMIT\" NAME=\"choosebook\" VALUE=\"Select Book\">";
		}
		elseif($searchTerm != "" && isset($searchTerm))
		{
			echo "<br><br><h3>Choose one of the following</h3>";
			$safeSearchVal = addslashes($searchVal);
			$SQLcommand = "SELECT UniqueID, Title, Author, ISBN, BookCase, Shelf, ASIN from ".$table." WHERE `".$searchTerm."` LIKE '%".$safeSearchVal."%' ORDER BY ".$sortBy." ".$orderBy;
			$SQLresult = MYSQL_QUERY($SQLcommand);

			echo "<table align=\"center\" width=85% border=\"1\">";
			echo "<tr><td></td><td><b>BookCase</b></td><td><b>Shelf</b></td><td><b>Title</b></td><td><b>Author</b></td><td><b>ISBN</b></td></tr>";
			while($row=mysql_fetch_array($SQLresult))
			{
				if($row[ASIN] != "")
				{
				echo "<tr><td><input type=\"radio\" name=\"chosen\" value=\"".$row[UniqueID]."\"></td><td>".$row[BookCase]."</td><td>".$row[Shelf]."</td><td><a href=\"http://www.amazon.com/dp/".$row[ASIN]."\" target=\"_blank\">".$row[Title]."</a></td><td>".$row[Author]."</td><td>".$row[ISBN]."</td></tr>";
				}
				else
				{
				echo "<tr><td><input type=\"radio\" name=\"chosen\" value=\"".$row[UniqueID]."\"></td><td>".$row[BookCase]."</td><td>".$row[Shelf]."</td><td>".$row[Title]."</td><td>".$row[Author]."</td><td>".$row[ISBN]."</td></tr>";
				}
			}
			echo "</table>";
			echo "<input style=\"position: fixed; bottom:5px;left:5px;\" type =\"SUBMIT\" NAME=\"choosebook\" VALUE=\"Select Book\">";
		}
	?>

	<?php
	}
	else
	{
	echo "<INPUT TYPE=\"SUBMIT\" NAME=\"cancel\" VALUE=\"Cancel edit\">";
	}


	if(isset($cancel))
	{
	unset($Title);
	unset($Author);
	unset($ISBN);
	unset($BookCase);
	unset($Shelf);
	unset($ASIN);
	unset($cancel);
	}
	?>
<?php

		if(isset($chosen))
		{
        	setcookie("tempUniqueID", $chosen);
    			$query = "SELECT Author, Title, ISBN, BookCase, Shelf, ASIN FROM ".$table." WHERE UniqueID = ".$chosen;
    			$result = mysql_query($query);
    			check_mysql();
    			$row = mysql_fetch_row($result);
				$Author = $row[0];
				$Title = $row[1];
				$ISBN  =$row[2];
				$BookCase = $row[3];
				$Shelf = $row[4];
				$ASIN = $row[5];
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
<BR>Book Case:
<BR><INPUT TYPE="TEXT" NAME="BookCase" VALUE="<?php echo $BookCase; ?>">
<BR>
<BR>Shelf Number:
<BR><INPUT TYPE="TEXT" NAME="Shelf" VALUE="<?php echo $Shelf; ?>">
<BR>
<BR>ASIN:
<BR><INPUT TYPE="TEXT" NAME="ASIN" VALUE="<?php echo $ASIN; ?>">
<BR>
<BR>
<br>

<?php
		}
check_mysql();


if (isset($update))
{
	$safeTitle = addslashes($Title);
	$safeAuthor = addslashes($Author);
	$query = "UPDATE $table SET Title='$safeTitle', Author='$safeAuthor', ISBN='$ISBN', BookCase='$BookCase', Shelf='$Shelf', ASIN='$ASIN' WHERE UniqueID = ".$_COOKIE['tempUniqueID'];
	$result = mysql_query($query);
	check_mysql();
	echo "<script language=\"javascript\"> alert(\"Record Updated!\")</script>";
	
	unset($Title);
	unset($Author);
	unset($ISBN);
	unset($BookCase);
	unset($Shelf);
	unset($ASIN);
	unset($cancel);

	echo "<script language=\"javascript\"> window.location = \"https://".$siteURL."/Eratosthenes/search\" </script>";
}
elseif (isset($delete))
{
	$query = "SELECT BookCase, Shelf FROM $table WHERE UniqueID = ".$_COOKIE['tempUniqueID'];
	$result = mysql_query($query);
	check_mysql();
	$row = mysql_fetch_row($result);
	check_mysql();
	if ($row[0] != "")
	{
		$tempBookCase = $row[0];
		$tempShelf  = $row[1];
	}


	$query = "DELETE FROM $table WHERE UniqueID = ".$_COOKIE['tempUniqueID'];
	$result = mysql_query($query);
    
	echo "<script language=\"javascript\"> alert(\"Record Deleted: (BookCase=$tempBookCase), (Shelf=$tempShelf)\")</script>";
	echo "<script language=\"javascript\"> window.location = \"https://".$siteURL."/Eratosthenes/search\" </script>";
}


?>

<FORM ENCTYPE="multipart/form-data" ACTION="index.php" METHOD="POST">
<?php
	if(isset($chosen))
	{
?>
<INPUT TYPE="SUBMIT" NAME="update" VALUE="Update Book&nbsp">
<INPUT TYPE="SUBMIT" NAME="delete" VALUE="Delete Book">
<?php
	}
?>
<br>
<br>
</FORM>
<br>
<br>
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

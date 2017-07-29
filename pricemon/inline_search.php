<HTML>
<BODY>
<FORM METHOD="POST" ACTION="../search/index.php">

<?php

$searchTerm = $_GET["searchTerm"];
$searchVal = $_GET["searchVal"];
$sortBy = 'BookCase';
$orderBy = 'ASC';

echo "<input type=\"hidden\" name=\"searchTerm\" value=\"".$searchTerm."\">";
echo "<input type=\"hidden\" name=\"searchVal\" value=\"".$searchVal."\">";
echo "<input type=\"hidden\" name=\"sortBy\" value=\"".$sortBy."\">";
echo "<input type=\"hidden\" name=\"orderBy\" value=\"".$orderBy."\">";
echo "</FORM>";
echo "<SCRIPT LANGUAGE=\"JavaScript\">document.forms[0].submit();</SCRIPT>";

?>

</BODY>
</HTML>

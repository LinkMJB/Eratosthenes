<?php
require "settings.cfg";

if(isset($_POST["rank_threshold"])) {
	$rank_threshold=$_POST["rank_threshold"];
}

if(isset($_POST["price_threshold"])) {
	$price_threshold=$_POST["price_threshold"];
}

//https://sellercentral.amazon.com/gp/upload-download-utils/requestReport.html?type=OpenListingReport
//https://sellercentral.amazon.com/gp/reports/documents/_GET_FLAT_FILE_OPEN_LISTINGS_DATA__18245311063.txt?ie=UTF8&contentType=text%2Fxls
?>

<HTML>
<HEAD>
<TITLE>Eratosthenes</TITLE>
<meta charset="utf-8">
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css">
<script>
$(function() {
	$( "#accordion" ).accordion({
		heightStyle: "content",
		collapsible: true
	});
});
</script>

</HEAD>
<BODY>
<center><button onclick="window.location='https://<?php echo "$siteURL" ?>/Eratosthenes';">Home</button></center>

<table width="100%">
<tr>
<td>
<div>
<FORM ENCTYPE="multipart/form-data" ACTION="uploader.php" METHOD="POST">
    <a href="https://sellercentral.amazon.com/gp/upload-download-utils/requestReport.html?type=OpenListingReport" target="_blank">Request Inventory from Amazon (CSV)</a><br>
    Upload inventory (to update price value monitoring):<br>
    <INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="1000000" />
    <INPUT NAME="uploaded_file" type="file" />
    <INPUT TYPE="submit" value="Upload" />
</FORM>
</div>
</td>
<td align="right">
<FORM ACTION="index.php" METHOD="POST">
Minimum sellable rank: <input type='text' name='rank_threshold' onchange="this.form.submit();" value="<?php echo $rank_threshold; ?>" size="8"><br>
Acceptable % above/below average book price: <input type='text' name='price_threshold' onchange="this.form.submit();" value="<?php echo $price_threshold; ?>" maxlength="2" size="2">
</FORM>
</td>
</tr>
</table>

<div style="position: fixed; bottom:5px;left:5px;" id="overlay">
<a href="refresh.php">Refresh Prices</a>
</div>

<button style="position: fixed; bottom:5px;right:5px;" onclick="window.location='#';">Back to top</button>

<?php
		require "summary.php";
?>
		
		<div id="accordion">
		
		<?php
		echo "<h3>".count($greenarr)." books are around ".$green_font."AVERAGE</font> price, and ".$green_font."BETTER</font> than rank '".$rank_threshold."'</h3>";
		echo "<div>";
		echo "<p>";
		echo "<br><b>(Hover over links to see price information)</b><br>";
		echo "<table align=\"center\" width=85% border=\"1\">";
		echo "<tr bgcolor=\"".$green_hex."\"><td><b>Title</b></td></tr>";
		foreach($greenarr as $line)
		{
			echo "$line";
		} 
		echo "</table>";
		echo "</p>";
		echo "</div>";

		echo "<h3>".count($purplearr)." books are ".$purple_font."MUCH CHEAPER</font> than competitive average price, and ".$purple_font."BETTER</font> than rank '".$rank_threshold."'</h3>";
		echo "<div>";
		echo "<p>";
		echo "<br><b>(Hover over links to see price information)</b><br>";
		echo "<table align=\"center\" width=85% border=\"1\">";
		echo "<tr bgcolor=\"".$purple_hex."\"><td><b>Title</b></td></tr>";
		foreach($purplearr as $line)
		{
			echo "$line";
		} 
		echo "</table>";
		echo "</p>";
		echo "</div>";

		echo "<h3>".count($bluearr)." books are ".$blue_font."ABOVE</font> average price, and ".$blue_font."BETTER</font> than rank '".$rank_threshold."'</h3>";
		echo "<div>";
		echo "<p>";
		echo "<br><b>(Hover over links to see price information)</b><br>";
		echo "<table align=\"center\" width=85% border=\"1\">";
		echo "<tr bgcolor=\"".$blue_hex."\"><td><b>Title</b></td></tr>";
		foreach($bluearr as $line)
		{
			echo "$line";
		} 
		echo "</table>";
		echo "</p>";
		echo "</div>";

		echo "<h3>".count($yellowarr)." books are ".$yellow_font."BELOW</font> average price, and ".$yellow_font."WORSE</font> than rank '".$rank_threshold."'</h3>";
		echo "<div>";
		echo "<p>";
		echo "<br><b>(Hover over links to see price information)</b><br>";
		echo "<table align=\"center\" width=85% border=\"1\">";
		echo "<tr bgcolor=\"".$yellow_hex."\"><td><b>Title</b></td></tr>";
		foreach($yellowarr as $line)
		{
			echo "$line";
		} 
		echo "</table>";
		echo "</p>";
		echo "</div>";

		echo "<h3>".count($redarr)." books are ".$red_font."ABOVE</font> average price, and ".$red_font."WORSE</font> than rank '".$rank_threshold."'</h3>";
		echo "<div>";
		echo "<p>";
		echo "<br><b>(Hover over links to see price information)</b><br>";
		echo "<table align=\"center\" width=85% border=\"1\">";
		echo "<tr bgcolor=\"".$red_hex."\"><td><b>Title</b></td></tr>";
		foreach($redarr as $line)
		{
			echo "$line";
		} 
		echo "</table>";
		echo "</p>";
		echo "</div>";

		echo "<h3>".count($brownarr)." books are ".$brown_font."NOT FOR SALE</font></h3>";
		echo "<div>";
		echo "<p>";
		echo "<table align=\"center\" width=85% border=\"1\">";
		echo "<tr bgcolor=\"".$brown_hex."\"><td><b>Title</b></td></tr>";
		foreach($brownarr as $line)
		{
			echo "$line";
		} 
		echo "</table>";
		echo "</p>";
		echo "</div>";

		echo "<h3>".count($greyarr)." books ".$grey_font."HAVE NO RANK</font></h3>";
		echo "<div>";
		echo "<p>";
		echo "<table align=\"center\" width=85% border=\"1\">";
		echo "<tr bgcolor=\"".$grey_hex."\"><td><b>Title</b></td></tr>";
		foreach($greyarr as $line)
		{
			echo "$line";
		} 
		echo "</table>";
		echo "</p>";
		echo "</div>";

		if (count($uncaughtarr) > 0)
		{
			echo "<h3>WARNING: ".count($uncaughtarr)." books did not match any rule!'</h3>";
			echo "<div>";
			echo "<p>";
			echo "<br><b>(Hover over links to see price information)</b><br>";
			echo "<table align=\"center\" width=85% border=\"1\">";
			echo "<tr bgcolor=\"".$uncaught_hex."\"><td><b>Title</b></td></tr>";
			foreach($uncaughtarr as $line)
			{
				echo "$line";
			} 
			echo "</table>";
			echo "</p>";
			echo "</div>";
		}

		echo "</div>";
		echo "</center>";
		

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

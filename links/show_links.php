<?php
include("../common_functions_v2.php");
include("graphsettings.php");
$dbi = std_dbi();

echo(html_header());

$query = "SELECT id,url,comment FROM short_links ORDER BY ID DESC"; 
$result = mysqli_query($dbi, $query);
echo("<table border='1' class=\"links\">"); 
echo("<tr><td><b>Id</b></td><td><b>Comment</b></td><td><b>Link</b></td></tr>"); 
while($row = mysqli_fetch_array($result)) { 
  echo("<tr><td>"); 
  echo($row['id']); 
  echo("</td><td>"); 
  echo($row['comment']);
  echo("</td><td>");
  echo("<a href=\"http://www.cinfdata.fysik.dtu.dk/links/link.php?id=" . $row['id'] . "\">cinfdata.fysik.dtu.dk/links/link.php?id=" . $row['id'] . "</a>");
  echo("</td></tr>");
} 
echo("</table>");

echo(html_footer());
?>

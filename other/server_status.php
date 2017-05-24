<H1>Cinfdata Server Status</H1>

<H2>PhP status</H2>
<?php
    # Define standard texts
    $not_found = "<span style=\"color:#FF0000;font-weight:bold\">Not Found</span>";
    $ok = "<span style=\"color:#04B404;font-weight:bold\">OK</span>";

    $yaml_support = function_exists("yaml_parse_file") ? $ok  : $not_found;
    echo("<p>YAML support: " . $yaml_support . "</p>");
?>

<H2>MySQL connection</H2>

<?php
    $found = false;
$found_host = "";
$user = "cinf_reader";
$sitesettings = yaml_parse_file(dirname(__FILE__) . "/../sitesettings.yaml");

try {
    $mysqli = new mysqli($sitesettings["db_hostname"], $user, $user, $sitesettings["db_name"], $port=$sitesettings["db_port"]);
    $found = true;
    $found_host = $sitesettings["db_hostname"] . ":" . $sitesettings["db_port"];
} catch (ErrorException $e) {}

// Form connection and return
try {
    $mysqli = new mysqli($sitesettings["dev_db_hostname"], $user, $user, $sitesettings["db_name"], $port=$sitesettings["dev_db_port"]);
    $found = true;
    $found_host = $sitesettings["dev_db_hostname"] . ":" . $sitesettings["dev_db_port"];
} catch (ErrorException $e) {}

if ($found){
    echo("<p>Database connection $ok</p>");
    echo("<p>Database connection used: $found_host</p>");
    
} else {

}
?>

<H2>Python status</H2>
<p>Status of required python modules</p>
<?php

$output = Array();
$return = exec("python --version 2>&1", $output, $status);
echo("<p>$output[0]</p>");

$required_module_list = ['numpy', 'scipy', 'matplotlib', 'MySQLdb', 'yaml'];
foreach ($required_module_list as $module){
    $output = Array();
    $return = exec("python -c \"import $module\"", $output, $status);
    echo("<p>$module: " . ($status == 0 ? $ok : $not_found) . "</p>");
}
    
?>
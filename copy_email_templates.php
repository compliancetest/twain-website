<?php
/**
* Copy Email Templates To Prod From Test Env
*/

$test_db_host = 'test.compliancetest.net';
$test_db_name = 'testcompliance';
$test_db_user = 'testcompliance';
$test_db_password = 'cb8682e0ff4721';

$test_db = mysql_connect($test_db_host, $test_db_user, $test_db_password, true) or die("Test DB Connection Error: " . mysql_error());
mysql_select_db($test_db_name, $test_db);

$live_db_host = 'compliancetest.cvno0ugmoa4w.ap-southeast-2.rds.amazonaws.com';
$live_db_name = 'compliancetestfront';
$live_db_user = 'dbadmin';
$live_db_password = 'W26pgueXAbMv7PdoJlTz';

/*$live_db_host = 'localhost';
$live_db_name = 'testcompliance';
$live_db_user = 'root';
$live_db_password = 'root';
*/
$live_db = mysql_connect($live_db_host, $live_db_user, $live_db_password, true) or die("Live DB Connection Error: " . mysql_error());
mysql_select_db($live_db_name, $live_db);

//Remove current Templates
mysql_query("DELETE FROM wp_options WHERE option_name LIKE '%email_title' OR option_name LIKE '%email_content'", $live_db);

//Getting All Email Templates
$query = "SELECT option_name, option_value, autoload FROM wp_options WHERE option_name LIKE '%email_title' OR option_name LIKE '%email_content' ORDER BY option_name";
$t_rs = mysql_query($query, $test_db);

while ($t_row = mysql_fetch_assoc($t_rs)) {
/*    echo "<b>Name:</b> " . $t_row['option_name'] ."<br />";
    echo "<b>Content:</b> " . $t_row['option_value'] ."<hr />";*/
    
    $query = "INSERT INTO `wp_options`
                (`option_name`, `option_value`, `autoload`)
              VALUES
                (
                    '" . mysql_real_escape_string($t_row['option_name'], $live_db) . "',
                    '" . mysql_real_escape_string($t_row['option_value'], $live_db) . "',
                    '" . mysql_real_escape_string($t_row['autoload'], $live_db) . "'
                )";
              
    mysql_query($query, $live_db) or die(mysql_error($live_db));
}

echo "<br /><b>Completed!</b>";
<?
##عماد
$db_hostname='voltaSalesDb.db.8352929.hostedresource.com';
$db_database='voltaSalesDb';
$db_username='voltaSalesDb';
$db_password='Elpurgy#201655';
$db_server=mysql_connect($db_hostname,$db_username,$db_password);
mysql_query("SET NAMES UTF8");
mysql_query("set characer set UTF8",$db_server);
//$dd=mysql_set_charset("UTF8",$db_server);
//$charset = mysql_client_encoding($db_server);
if(!$db_server)die($error[10001].mysql_error());
//$charset = mysql_client_encoding($db_server);
//echo "The current character set is: $charset\n";
mysql_select_db($db_database)or die ($error[10002].mysql_error());
?>
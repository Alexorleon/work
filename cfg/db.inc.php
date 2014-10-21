<?php
define ( 'db_user', 'STAT' );
define ( 'db_pass', 'CNFN' );	
define ( 'db_db', 'localhost/XE' );
//print db_user."__".db_pass;
$user = db_user;
$pass = db_pass;
$db = db_db;
if (!@$c = OCILogon($user, $pass, $db, 'CL8MSWIN1251')) {
	$smarty->assign('title','Нет связи');
	$smarty->display('not_connect.tpl.html'); 
	die(); 
}
?>
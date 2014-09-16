<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php"); 
	
	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if ($_POST){
		
	}

	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Ламповая");
	$smarty->display("lamp.tpl.html");

	// --- ФУНКЦИИ ---

  ?>
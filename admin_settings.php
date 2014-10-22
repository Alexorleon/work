<?php	
	unset($_SESSION);
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");
	
	/*if( !isset($_SESSION['admin_access'])){
	
		die('<script>document.location.href= "'.lhost.'/login"</script>');
	}*/
	//unset($_SESSION['admin_access']);
	
	$db = new db;
	$db->GetConnect();
	$error_='';
	
	if($_GET){
	}
	
	
	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Настройки");
	$smarty->display("admin_settings.tpl.html");

	// --- ФУНКЦИИ ---

  ?>
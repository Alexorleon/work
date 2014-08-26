<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if ($_POST){

		$type_personal = $_POST['type_personal'];

		if ($type_personal == 0){ // новые документы

			// переходим в лобби
			die('<script>document.location.href= "'.lhost.'/index.php"</script>');
		}
	}
	
	// TODO: получить всю статистику сотрудника
	
	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Персональные данные");
	$smarty->display("personal_data.tpl.html");
?>
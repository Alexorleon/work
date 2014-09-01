<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if ($_POST){

		$type_proposals = $_POST['type_proposals'];

		if ($type_proposals == 0){ // новые документы

			// переходим в лобби
			die('<script>document.location.href= "'.lhost.'/index.php"</script>');
		}
	}
	
	// TODO: получить историю отправленных сообщений (и показать прочитанные)
	
	$smarty->assign("error_", $error_);

	$smarty->assign("title", "Предложения руководству");
	$smarty->display("proposals.tpl.html");
?>
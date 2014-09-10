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
		}elseif($type_proposals == 1){
		
			// отправляем сообщение - добавляем в БД.
			print_r("send send send");
			
			// доложить об успешности или не успешности отправления, но не обновлять страницу!
			
		}else{}
	}
	
	// получаем типы сообщения			
	$sql = <<<SQL
	SELECT ID, TITLE FROM stat.PROPOSALS_TYPEMES
SQL;
	$array_typemes = $db->go_result($sql);

	// TODO: получить историю отправленных сообщений (и показать прочитанные)
	
	$smarty->assign("error_", $error_);
	$smarty->assign("array_typemes", $array_typemes);

	$smarty->assign("title", "Предложения руководству");
	$smarty->display("proposals.tpl.html");
?>
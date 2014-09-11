<?php
	require_once($_SERVER['DOCUMENT_ROOT']."./cfg/config.inc.php");

	$db = new db;
	$db->GetConnect();
	$error_='';
		
	if ($_POST){
		
		$type_proposals = $_POST['type_proposals'];

		if ($type_proposals == 0){

			// переходим в лобби
			die('<script>document.location.href= "'.lhost.'/index.php"</script>');
		}elseif($type_proposals == 1){
		
			// записываем предложение
			$sotrudID = $_SESSION['sotrud_id'];
			$typemessage = $_POST['typemessage'];
			$message = iconv("utf-8", "windows-1251", $_POST['tabnum']);
			$current_date = date('d.m.y H:i:s');

			$sql = <<<SQL
				INSERT INTO stat.PROPOSALS (SOTRUDID, PROPOSAL_TYPEMESID, PROPOSAL, DATE_SENT) VALUES 
				('$sotrudID', 
				'$typemessage', 
				'$message', 
				to_date('$current_date', 'DD.MM.YYYY HH24:MI:SS'))
SQL;
			$db->go_query($sql);
			
			// TODO: доложить об успешности
			
		}else{}
	}
	
	// получаем типы сообщения
	$sql = <<<SQL
	SELECT ID, TITLE FROM stat.PROPOSALS_TYPEMES
SQL;
	$array_typemes = $db->go_result($sql);
		
	// разделим на 2 меню. статистику и добавление нового предложения.
	if(isset($_GET['type_prop'])){
	
		if($_GET['type_prop'] == 0){ // выводим статистику
		
			/*$sotrudID = $_SESSION['sotrud_id'];
			
			// собираем статистику
			$sql = <<<SQL
			SELECT PROPOSAL FROM stat.PROPOSALS WHERE PROPOSALS.ID='$sotrudID'
SQL;
			$statistic = $db->go_result($sql);
			
			print_r();
			die();*/
			
			$smarty->assign("type_prop", "view");
		}elseif($_GET['type_prop'] == 1){ // добавляем новое предложение
		
			$smarty->assign("type_prop", "add");
		}else{}
	}
		
	$smarty->assign("error_", $error_);
	$smarty->assign("array_typemes", $array_typemes);

	$smarty->assign("title", "Предложения руководству");
	$smarty->display("proposals.tpl.html");
?>